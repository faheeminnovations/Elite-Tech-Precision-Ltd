<x-app-layout>
    <div class="card ef p-3">
        <div class="section-head">
            <h2>Settings</h2>
            <div class="section-sub">Ireland deployment, email alerts, PPM defaults, users and security configuration</div>
        </div>

        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- PPM Defaults --}}
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Default PPM frequency</label>
                    <select class="form-select" style="max-width:260px;" name="default_ppm_frequency">
                        <option value="6 months" {{ old('default_ppm_frequency', session('settings.default_ppm_frequency', '6 months')) === '6 months' ? 'selected' : '' }}>Every 6 months</option>
                        <option value="quarterly" {{ old('default_ppm_frequency', session('settings.default_ppm_frequency', '6 months')) === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="annually" {{ old('default_ppm_frequency', session('settings.default_ppm_frequency', '6 months')) === 'annually' ? 'selected' : '' }}>Annually</option>
                        <option value="custom" {{ old('default_ppm_frequency', session('settings.default_ppm_frequency', '6 months')) === 'custom' ? 'selected' : '' }}>Custom…</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Internal reminder inbox</label>
                    <input class="form-control mono {{ $errors->has('internal_reminder_inbox') ? 'is-invalid' : '' }}" style="max-width:300px;" type="email" name="internal_reminder_inbox" value="{{ old('internal_reminder_inbox', session('settings.internal_reminder_inbox', 'info@elitetechprecision.ie')) }}">
                    @error('internal_reminder_inbox')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Internal reminder lead time</label>
                    <input class="form-control" style="max-width:200px;" type="text" name="internal_reminder_lead_time" value="{{ old('internal_reminder_lead_time', session('settings.internal_reminder_lead_time', '30 days before due')) }}" placeholder="e.g., 30 days before due">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer reminder lead time</label>
                    <input class="form-control" style="max-width:200px;" type="text" name="customer_reminder_lead_time" value="{{ old('customer_reminder_lead_time', session('settings.customer_reminder_lead_time', '30 days before due')) }}" placeholder="e.g., 30 days before due">
                </div>
            </div>

            {{-- Ireland Deployment --}}
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Country / Deployment</label>
                    <select class="form-select" name="country">
                        <option value="Ireland" {{ old('country', session('settings.country', 'Ireland')) === 'Ireland' ? 'selected' : '' }}>Ireland</option>
                        <option value="United Kingdom" {{ old('country', session('settings.country', 'Ireland')) === 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Timezone</label>
                    <select class="form-select" name="timezone">
                        <option value="Europe/Dublin" {{ old('timezone', session('settings.timezone', 'Europe/Dublin')) === 'Europe/Dublin' ? 'selected' : '' }}>Europe/Dublin</option>
                        <option value="Europe/London" {{ old('timezone', session('settings.timezone', 'Europe/Dublin')) === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Currency</label>
                    <select class="form-select" name="currency">
                        <option value="EUR" {{ old('currency', session('settings.currency', 'EUR')) === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                        <option value="GBP" {{ old('currency', session('settings.currency', 'EUR')) === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Date format</label>
                    <select class="form-select" name="date_format">
                        <option value="DD/MM/YYYY" {{ old('date_format', session('settings.date_format', 'DD/MM/YYYY')) === 'DD/MM/YYYY' ? 'selected' : '' }}>DD/MM/YYYY</option>
                        <option value="MM/DD/YYYY" {{ old('date_format', session('settings.date_format', 'DD/MM/YYYY')) === 'MM/DD/YYYY' ? 'selected' : '' }}>MM/DD/YYYY</option>
                        <option value="YYYY-MM-DD" {{ old('date_format', session('settings.date_format', 'DD/MM/YYYY')) === 'YYYY-MM-DD' ? 'selected' : '' }}>YYYY-MM-DD</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Reminder email</label>
                    <input class="form-control {{ $errors->has('reminder_email') ? 'is-invalid' : '' }}" type="email" name="reminder_email" value="{{ old('reminder_email', session('settings.reminder_email', 'info@elitetechprecision.ie')) }}">
                    @error('reminder_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Reminder lead time</label>
                    <input class="form-control" type="text" name="reminder_lead_time" value="{{ old('reminder_lead_time', session('settings.reminder_lead_time', '30 days')) }}" placeholder="e.g., 30 days">
                </div>
            </div>

            {{-- Email Notifications --}}
            <div class="mt-4" style="border-top:1px solid var(--line);padding-top:20px;">
                <div class="section-head mb-3">
                    <h2>Email Notifications</h2>
                    <div class="section-sub">Configure automatic email alerts for status changes, actions, and reminders</div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="enable_email_notifications" value="0">
                            <input class="form-check-input" type="checkbox" name="enable_email_notifications" id="enableEmailNotifications" value="1" {{ old('enable_email_notifications', session('settings.enable_email_notifications', true)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="enableEmailNotifications">
                                Enable Email Notifications
                            </label>
                        </div>
                        <small class="text-muted">Send email alerts for all status changes, actions, and reminders</small>
                    </div>
                </div>

                {{-- Email Type Configuration --}}
                <div class="mt-4">
                    <h4>Email Type Configuration</h4>
                    <p class="text-muted small">Select which specific actions should trigger email notifications</p>
                    
                    @foreach($emailSettings as $category => $settings)
                        @if($settings->count() > 0)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        @if($category === 'reminder')
                                            <i class="bi bi-bell me-2"></i>Reminder Notifications
                                        @else
                                            {{ ucfirst($category) }} Notifications
                                        @endif
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @foreach($settings as $setting)
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="email_notifications[{{ $setting->email_type }}]" 
                                                   id="email_{{ $setting->email_type }}"
                                                   value="1"
                                                   {{ $setting->is_enabled ? 'checked' : '' }}>
                                            <label class="form-check-label" for="email_{{ $setting->email_type }}">
                                                {{ $setting->display_name }}
                                            </label>
                                            <div class="text-muted small">{{ $setting->description }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- User Management --}}
            <div class="mt-4" style="border-top:1px solid var(--line);padding-top:20px;">
                <div class="section-head mb-3">
                    <h2>User Management</h2>
                    <div class="section-sub">Manage system users and role assignments</div>
                </div>
                <div class="table-responsive">
                    <table class="ef-table">
                        <thead>
                            <tr><th>User</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="cell-primary">{{ Auth::user()->name ?? 'Admin' }}</td>
                                <td class="mono cell-sub">{{ Auth::user()->email ?? '—' }}</td>
                                <td><span class="tag tag-active"><span>Admin</span></span></td>
                                <td><span class="tag tag-completed"><span>Active</span></span></td>
                                <td><div class="crud-actions"><button class="btn btn-edit"><i class="bi bi-pencil"></i></button></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- System Info --}}
            <div class="empty-note mt-4">
                <i class="bi bi-shield-check d-block mb-2"></i>
                Ireland-ready UI profile: Dublin timezone, EUR currency, DD/MM/YYYY date format and configurable customer/internal PPM alerts.
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-ef-outline">Reset to Defaults</button>
                <button type="submit" class="btn btn-ef-primary"><i class="bi bi-save me-1"></i> Save Settings</button>
            </div>
        </form>
    </div>
</x-app-layout>
