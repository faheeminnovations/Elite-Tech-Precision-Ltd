<x-app-layout>
    {{-- KPI Row --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="kpi k-blue">
                <div class="kpi-icon"><i class="bi bi-calendar-week"></i></div>
                <div class="label">Upcoming PPMs</div>
                <div class="value">{{ $upcomingPpms }}</div>
                <div class="meta"><i class="bi bi-arrow-up-short text-success"></i>Due within 30 days</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi k-red">
                <div class="kpi-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="label">Overdue PPMs</div>
                <div class="value">{{ $overduePpms }}</div>
                <div class="meta">Needs scheduling now</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi k-amber">
                <div class="kpi-icon"><i class="bi bi-geo-alt"></i></div>
                <div class="label">Areas with PPMs</div>
                <div class="value">{{ $areaSummary->count() }}</div>
                <div class="meta">Next 3 weeks</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi k-orange">
                <div class="kpi-icon"><i class="bi bi-building"></i></div>
                <div class="label">Chain / New</div>
                <div class="value">{{ $chainCustomers }} / {{ $newCustomers }}</div>
                <div class="meta">Customer categories</div>
            </div>
        </div>
    </div>

    {{-- Area PPM Schedule --}}
    <div class="card ef p-3 mb-3">
        <div class="section-head" style="justify-content:space-between;display:flex;">
            <div>
                <h2>Area PPM Schedule — Next 3 Weeks</h2>
                <div class="section-sub">Plan visits by area to minimise travel and fuel costs</div>
            </div>
            <a href="{{ route('reports', ['type' => 'area-ppm-schedule']) }}" class="btn-ef-outline text-decoration-none">Full report <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="ef-table">
                <thead>
                    <tr>
                        <th>Week</th>
                        <th>Area</th>
                        <th>Chain Customers</th>
                        <th>New Customers</th>
                        <th>PPMs</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($areaSchedule as $row)
                        <tr>
                            <td>
                                <div class="cell-primary">{{ $row['week_label'] }}</div>
                                <div class="cell-sub">{{ $row['week_range'] }}</div>
                            </td>
                            <td class="cell-primary">{{ $row['area'] }}</td>
                            <td class="cell-sub">
                                @if(count($row['chain_customers']))
                                    {{ implode(', ', $row['chain_customers']) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="cell-sub">
                                @if(count($row['new_customers']))
                                    {{ implode(', ', $row['new_customers']) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td><span class="tag tag-upcoming"><span>{{ $row['ppm_count'] }}</span></span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-note">
                                    <i class="bi bi-geo-alt d-block mb-2"></i>
                                    No PPMs scheduled in the next 3 weeks. Add contracts with due dates to see area schedules.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pipeline Strip --}}
    <div class="card ef p-3 mb-3">
        <div class="section-head">
            <div>
                <h2>PPM pipeline this quarter</h2>
                <div class="section-sub">Status of all planned preventative maintenance visits</div>
            </div>
        </div>
        <div class="timeline-strip">
            <div class="ts-seg">
                <div class="n">{{ $pipelineScheduled }}</div>
                <div class="lbl">Scheduled</div>
                <div class="bar" style="background:var(--steel);width:100%"></div>
            </div>
            <div class="ts-seg">
                <div class="n">{{ $pipelineReminderSent }}</div>
                <div class="lbl">Reminder Sent</div>
                <div class="bar" style="background:var(--orange);width:82%"></div>
            </div>
            <div class="ts-seg">
                <div class="n">{{ $pipelineAccepted }}</div>
                <div class="lbl">Customer Accepted</div>
                <div class="bar" style="background:var(--success);width:64%"></div>
            </div>
            <div class="ts-seg">
                <div class="n">{{ $pipelineOverdue }}</div>
                <div class="lbl">Overdue</div>
                <div class="bar" style="background:var(--danger);width:26%"></div>
            </div>
            <div class="ts-seg">
                <div class="n">{{ $pipelineCompletedYtd }}</div>
                <div class="lbl">Completed YTD</div>
                <div class="bar" style="background:#8FA3C4;width:100%"></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Upcoming PPMs Table --}}
        <div class="col-lg-8">
            <div class="card ef p-3 mb-3">
                <div class="section-head" style="justify-content:space-between;display:flex;">
                    <div>
                        <h2>Upcoming & overdue PPMs</h2>
                        <div class="section-sub">Next scheduled visits across all sites</div>
                    </div>
                    <a href="{{ route('contracts.index') }}" class="btn-ef-outline" style="text-decoration:none;">View all <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="ef-table">
                        <thead><tr><th>Customer / Site</th><th>Contract</th><th>Frequency</th><th>Next Due</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($upcomingContracts as $contract)
                                <tr>
                                    <td>
                                        <div class="cell-primary">{{ $contract->customer_name }}</div>
                                        <div class="cell-sub">{{ $contract->site_name ?? '—' }}</div>
                                    </td>
                                    <td class="mono cell-sub">{{ $contract->contract_ref }}</td>
                                    <td>{{ $contract->frequency }}</td>
                                    <td class="mono cell-primary">{{ $contract->next_ppm_due ? $contract->next_ppm_due->format('d M Y') : '—' }}</td>
                                    <td>
                                        @php
                                            $st = $contract->status ?? 'upcoming';
                                            $map = ['upcoming'=>'tag-upcoming','overdue'=>'tag-overdue','active'=>'tag-completed','expiring'=>'tag-pending'];
                                            $lbl = ['upcoming'=>'Upcoming','overdue'=>'Overdue','active'=>'Active','expiring'=>'Expiring'];
                                        @endphp
                                        <span class="tag {{ $map[$st] ?? 'tag-upcoming' }}"><span>{{ $lbl[$st] ?? ucfirst($st) }}</span></span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5"><div class="empty-note"><i class="bi bi-check-circle d-block mb-2"></i>No upcoming PPMs at this time.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Recent Responses --}}
            <div class="card ef p-3 mb-3">
                <div class="section-head"><h2>Recent customer responses</h2></div>
                @forelse($recentResponses as $resp)
                    <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid var(--line);">
                        <div>
                            <div class="cell-primary" style="font-size:13px;">{{ $resp->customer_name }}</div>
                            <div class="cell-sub">{{ $resp->site_name ?? '—' }}</div>
                        </div>
                        @php
                            $rmap = ['accepted'=>'tag-accepted','declined'=>'tag-declined','awaiting'=>'tag-awaiting'];
                            $rlbl = ['accepted'=>'Accepted','declined'=>'Declined','awaiting'=>'Awaiting'];
                        @endphp
                        <span class="tag {{ $rmap[$resp->response] ?? 'tag-awaiting' }}"><span>{{ $rlbl[$resp->response] ?? ucfirst($resp->response) }}</span></span>
                    </div>
                @empty
                    <div class="cell-sub text-center py-3">No responses yet.</div>
                @endforelse
            </div>

            {{-- Reminder Activity --}}
            <div class="card ef p-3">
                <div class="section-head"><h2>Reminder activity</h2><div class="section-sub">Automated emails, last 7 days</div></div>
                <div class="stat-strip mb-2">
                    <div class="s"><b>{{ $reminderInternal }}</b><span>Internal</span></div>
                    <div class="s"><b>{{ $reminderCustomer }}</b><span>Customer</span></div>
                    <div class="s"><b>{{ $reminderResponded }}</b><span>Responded</span></div>
                </div>
                <div class="cell-sub">Internal reminders fire 30 days before due date to <span class="mono">info@elitetechprecision.ie</span>. Customer emails send at the same trigger with Accept / Decline actions.</div>
            </div>
        </div>
    </div>
</x-app-layout>
