<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResponseController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'response', 'date_from', 'date_to']);

        $responses = Response::query()
            ->filter($filters)
            ->latest()
            ->get();

        return view('responses.index', [
            'responses' => $responses,
            'filters' => $filters,
            'responseTypes' => Response::RESPONSE_TYPES,
        ]);
    }

    public function create(): View
    {
        return view('responses.create', [
            'customers' => Customer::orderBy('name')->get(['name', 'email', 'region']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateResponse($request);

        Response::create($validated);

        return $this->redirectAfterSave($request, 'Response recorded successfully.');
    }

    public function show(Response $response): View
    {
        return view('responses.show', compact('response'));
    }

    public function edit(Response $response): View
    {
        return view('responses.edit', [
            'response' => $response,
            'customers' => Customer::orderBy('name')->get(['name', 'email', 'region']),
        ]);
    }

    public function update(Request $request, Response $response): RedirectResponse
    {
        $validated = $this->validateResponse($request);

        $response->update($validated);

        return redirect()->route('responses.index')->with('success', 'Response updated successfully.');
    }

    public function destroy(Response $response): RedirectResponse
    {
        $response->delete();

        return redirect()->route('responses.index')->with('success', 'Response deleted successfully.');
    }

    private function validateResponse(Request $request): array
    {
        return $request->validate([
            'ppm_reference' => ['nullable', 'string', 'max:100'],
            'contract_ref' => ['nullable', 'string', 'max:100'],
            'job_ref' => ['nullable', 'string', 'max:100'],
            'customer_name' => ['required', 'string', 'max:255'],
            'site_name' => ['nullable', 'string', 'max:255'],
            'ppm_due' => ['nullable', 'date'],
            'reminder_sent' => ['nullable', 'date'],
            'response' => ['nullable', 'string', 'max:50'],
            'responded_on' => ['nullable', 'date'],
            'response_time' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string'],
            'internal_notification_sent' => ['nullable', 'string', 'in:yes,no'],
        ]);
    }

    private function redirectAfterSave(Request $request, string $message): RedirectResponse
    {
        if ($request->input('return_to') === 'data-entry') {
            return redirect()->route('data-entry')->with('success', $message);
        }

        return redirect()->route('responses.index')->with('success', $message);
    }
}
