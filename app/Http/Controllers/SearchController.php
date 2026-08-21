<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Response;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->get('q', ''));

        $results = [
            'customers' => collect(),
            'contracts' => collect(),
            'services' => collect(),
            'responses' => collect(),
        ];

        if (strlen($query) >= 2) {
            $filters = ['search' => $query];

            $results = [
                'customers' => Customer::filter($filters)->orderBy('name')->limit(15)->get(),
                'contracts' => Contract::filter($filters)->orderBy('customer_name')->limit(15)->get(),
                'services' => Service::filter($filters)->orderBy('visit_date', 'desc')->limit(15)->get(),
                'responses' => Response::filter($filters)->orderBy('ppm_due', 'desc')->limit(15)->get(),
            ];
        }

        $total = collect($results)->sum(fn ($items) => $items->count());

        return view('search.index', compact('query', 'results', 'total'));
    }
}
