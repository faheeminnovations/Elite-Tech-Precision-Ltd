<x-app-layout>
    <div class="card ef p-3">
        <div class="section-head">
            <h2>Settings</h2>
            <div class="section-sub">Ireland deployment, email alerts, PPM defaults, users and security configuration</div>
        </div>

        {{-- PPM Defaults --}}
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Default PPM frequency</label>
                <select class="form-select" style="max-width:260px;">
                    <option>Every 6 months</option>
                    <option>Quarterly</option>
                    <option>Annually</option>
                    <option>Custom…</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Internal reminder inbox</label>
                <input class="form-control mono" style="max-width:300px;" value="info@elitetechprecision.ie">
            </div>
            <div class="col-md-6">
                <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Internal reminder lead time</label>
                <input class="form-control" style="max-width:200px;" value="30 days before due">
            </div>
            <div class="col-md-6">
                <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer reminder lead time</label>
                <input class="form-control" style="max-width:200px;" value="30 days before due">
            </div>
        </div>

        {{-- Ireland Deployment --}}
        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Country / Deployment</label>
                <select class="form-select">
                    <option selected>Ireland</option>
                    <option>United Kingdom</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Timezone</label>
                <select class="form-select">
                    <option selected>Europe/Dublin</option>
                    <option>Europe/London</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Currency</label>
                <select class="form-select">
                    <option selected>EUR (€)</option>
                    <option>GBP (£)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Date format</label>
                <select class="form-select">
                    <option selected>DD/MM/YYYY</option>
                    <option>MM/DD/YYYY</option>
                    <option>YYYY-MM-DD</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Reminder email</label>
                <input class="form-control" value="info@elitetechprecision.ie">
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Reminder lead time</label>
                <input class="form-control" value="30 days">
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
            <button class="btn btn-ef-outline">Reset to Defaults</button>
            <button class="btn btn-ef-primary"><i class="bi bi-save me-1"></i> Save Settings</button>
        </div>
    </div>
</x-app-layout>
