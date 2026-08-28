<?php

namespace App\Http\Controllers;

use App\Models\EmailNotificationSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $emailSettings = EmailNotificationSetting::getByCategory();
        
        return view('settings', compact('emailSettings'));
    }

    public function update(Request $request)
    {
        // Validate the settings
        $validated = $request->validate([
            'default_ppm_frequency' => 'required|string',
            'internal_reminder_inbox' => 'required|email',
            'internal_reminder_lead_time' => 'required|string',
            'customer_reminder_lead_time' => 'required|string',
            'country' => 'required|string',
            'timezone' => 'required|string',
            'currency' => 'required|string',
            'date_format' => 'required|string',
            'reminder_email' => 'required|email',
            'reminder_lead_time' => 'required|string',
            'enable_email_notifications' => 'nullable|boolean',
        ], [
            'internal_reminder_inbox.required' => 'The internal reminder inbox field is required.',
            'internal_reminder_inbox.email' => 'The internal reminder inbox must be a valid email address.',
            'reminder_email.required' => 'The reminder email field is required.',
            'reminder_email.email' => 'The reminder email must be a valid email address.',
            'internal_reminder_lead_time.required' => 'The internal reminder lead time field is required.',
            'customer_reminder_lead_time.required' => 'The customer reminder lead time field is required.',
            'reminder_lead_time.required' => 'The reminder lead time field is required.',
        ]);

        // Handle checkbox - if not present, set to false
        $validated['enable_email_notifications'] = $request->has('enable_email_notifications');

        // Store settings in session
        session(['settings' => $validated]);

        // Update email notification settings
        if ($request->has('email_notifications')) {
            foreach ($request->input('email_notifications', []) as $emailType => $isEnabled) {
                if ($isEnabled === '1' || $isEnabled === true) {
                    EmailNotificationSetting::enable($emailType);
                } else {
                    EmailNotificationSetting::disable($emailType);
                }
            }
        }

        return redirect()->route('settings')->with('success', 'Settings updated successfully.');
    }

    /**
     * Get email notification settings as JSON
     */
    public function getEmailSettings()
    {
        $emailSettings = EmailNotificationSetting::all();
        return response()->json($emailSettings);
    }

    /**
     * Update a single email notification setting
     */
    public function updateEmailSetting(Request $request, string $emailType)
    {
        $request->validate([
            'is_enabled' => 'required|boolean',
        ]);

        if ($request->is_enabled) {
            EmailNotificationSetting::enable($emailType);
        } else {
            EmailNotificationSetting::disable($emailType);
        }

        return response()->json([
            'success' => true,
            'message' => 'Email notification setting updated successfully.',
        ]);
    }
}