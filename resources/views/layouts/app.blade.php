<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EliteFlow — {{ config('app.name', 'Elite Tech Precision Ltd') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --navy:#0B2545;
            --steel:#13315C;
            --steel-light:#2E4C7A;
            --orange:#FF6B35;
            --orange-dim:#FFE4D6;
            --paper:#F3F5F8;
            --card:#FFFFFF;
            --line:#E2E7EF;
            --ink:#0B2545;
            --ink-soft:#5B6B85;
            --success:#1E8E5A;
            --success-bg:#E4F5EC;
            --warning:#B96A00;
            --warning-bg:#FFF1DE;
            --danger:#C0392B;
            --danger-bg:#FCE7E4;
            --mono:'JetBrains Mono', monospace;
            --disp:'Barlow Condensed', sans-serif;
            --body:'Inter', sans-serif;
        }
        *{box-sizing:border-box;}
        body{
            font-family:var(--body);
            background:var(--paper);
            color:var(--ink);
            margin:0;
            font-size:14.5px;
        }
        h1,h2,h3,h4,h5,.disp{font-family:var(--disp);letter-spacing:0.2px;}
        .mono{font-family:var(--mono);}

        #shell{display:flex;min-height:100vh;}

        /* Sidebar */
        #sidebar{
            width:230px;background:var(--navy);color:#CBD8EE;flex-shrink:0;
            display:flex;flex-direction:column;position:sticky;top:0;height:100vh;
        }
        .brand{display:flex;align-items:center;gap:10px;padding:20px 18px;border-bottom:1px solid rgba(255,255,255,0.08);flex-shrink:0;}
        .brand-mark{
            width:34px;height:34px;background:var(--orange);border-radius:6px;
            display:flex;align-items:center;justify-content:center;font-family:var(--disp);font-weight:700;color:var(--navy);font-size:18px;
        }
        .brand-text{line-height:1.1;}
        .brand-text .top{font-family:var(--disp);font-weight:600;font-size:17px;color:#fff;letter-spacing:0.5px;}
        .brand-text .sub{font-size:10.5px;color:#8FA3C4;text-transform:uppercase;letter-spacing:1px;}

        .nav-container{flex:1;overflow-y:auto;padding:8px 0;scrollbar-width:none;-ms-overflow-style:none;}
        .nav-container::-webkit-scrollbar{display:none;}
        .nav-group{padding:8px 10px;}
        .nav-label{font-size:10.5px;text-transform:uppercase;letter-spacing:1.2px;color:#6E86AE;padding:4px 12px 4px;margin-top:4px;}
        .nav-link.eliteflow{
            display:flex;align-items:center;gap:10px;color:#B9C8E0;padding:9px 12px;
            border-radius:7px;font-size:13.8px;font-weight:500;margin-bottom:2px;cursor:pointer;
            transition:background .12s ease, color .12s ease;text-decoration:none;
        }
        .nav-link.eliteflow i{font-size:15px;width:18px;text-align:center;}
        .nav-link.eliteflow:hover{background:rgba(255,255,255,0.06);color:#fff;}
        .nav-link.eliteflow.active{background:var(--orange);color:#1a1300;font-weight:600;}
        .nav-link.eliteflow .badge-count{
            margin-left:auto;background:rgba(255,255,255,0.14);color:#fff;
            border-radius:20px;font-size:11px;padding:1px 7px;font-family:var(--mono);
        }
        .nav-link.eliteflow.active .badge-count{background:rgba(0,0,0,0.18);color:#1a1300;}

        .sidebar-foot{padding:14px 18px;border-top:1px solid rgba(255,255,255,0.08);font-size:11.5px;color:#6E86AE;flex-shrink:0;}

        /* Main column */
        #main{flex:1;min-width:0;display:flex;flex-direction:column;}
        #topbar{
            background:var(--card);border-bottom:1px solid var(--line);padding:14px 26px;
            display:flex;align-items:center;gap:16px;position:sticky;top:0;z-index:20;
        }
        #topbar .page-title{font-family:var(--disp);font-size:22px;font-weight:600;color:var(--navy);}
        #topbar .search-box{max-width:320px;flex:1;position:relative;}
        #topbar .search-box input{border-radius:8px;border:1px solid var(--line);background:var(--paper);padding:8px 12px 8px 34px;font-size:13px;width:100%;}
        #topbar .search-box i{position:absolute;left:11px;top:9px;color:var(--ink-soft);}
        .topbar-actions{margin-left:auto;display:flex;align-items:center;gap:14px;}
        .icon-btn{
            width:36px;height:36px;border-radius:8px;background:var(--paper);
            display:flex;align-items:center;justify-content:center;color:var(--steel);
            position:relative;cursor:pointer;border:1px solid var(--line);background:transparent;
        }
        .icon-btn .dot{position:absolute;top:-3px;right:-3px;width:9px;height:9px;background:var(--orange);border-radius:50%;border:2px solid var(--card);}
        .user-chip{display:flex;align-items:center;gap:8px;padding-left:10px;border-left:1px solid var(--line);cursor:pointer;}
        .user-ava{width:34px;height:34px;border-radius:8px;background:var(--steel);color:#fff;display:flex;align-items:center;justify-content:center;font-family:var(--disp);font-weight:600;}
        .user-chip .name{font-size:13px;font-weight:600;line-height:1.1;}
        .user-chip .role{font-size:11px;color:var(--ink-soft);}

        #content{padding:24px 26px 60px;}

        /* Cards */
        .card.ef{background:var(--card);border:1px solid var(--line);border-radius:12px;box-shadow:0 1px 2px rgba(11,37,69,0.03);}
        .section-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;}
        .section-head h2{font-size:19px;margin:0;color:var(--navy);}
        .section-sub{color:var(--ink-soft);font-size:13px;margin-top:2px;}

        /* KPI */
        .kpi{padding:18px 18px 16px;border-radius:12px;background:var(--card);border:1px solid var(--line);position:relative;overflow:hidden;height:100%;}
        .kpi::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;}
        .kpi.k-orange::before{background:var(--orange);}
        .kpi.k-red::before{background:var(--danger);}
        .kpi.k-amber::before{background:var(--warning);}
        .kpi.k-blue::before{background:var(--steel);}
        .kpi .label{font-size:11.5px;text-transform:uppercase;letter-spacing:0.8px;color:var(--ink-soft);font-weight:600;}
        .kpi .value{font-family:var(--disp);font-size:36px;font-weight:700;color:var(--navy);line-height:1.15;margin-top:2px;}
        .kpi .meta{font-size:12px;color:var(--ink-soft);margin-top:4px;}
        .kpi .meta i{margin-right:3px;}
        .kpi .kpi-icon{
            position:absolute;right:14px;top:16px;width:34px;height:34px;border-radius:9px;
            display:flex;align-items:center;justify-content:center;font-size:16px;
        }
        .k-orange .kpi-icon{background:var(--orange-dim);color:#B84D1B;}
        .k-red .kpi-icon{background:var(--danger-bg);color:var(--danger);}
        .k-amber .kpi-icon{background:var(--warning-bg);color:var(--warning);}
        .k-blue .kpi-icon{background:#E4EBF6;color:var(--steel);}

        /* Status tag badge */
        .tag{
            display:inline-flex;align-items:center;gap:5px;font-family:var(--mono);font-size:11px;font-weight:500;
            padding:3px 9px 3px 12px;position:relative;border-radius:2px 6px 6px 2px;line-height:1.6;white-space:nowrap;
        }
        .tag::before{content:'';position:absolute;left:4px;top:50%;transform:translateY(-50%);width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,0.7);box-shadow:0 0 0 1px rgba(0,0,0,0.08) inset;}
        .tag::after{content:'';position:absolute;left:0;top:0;bottom:0;width:10px;background:inherit;clip-path:polygon(0 50%, 60% 0, 100% 0, 100% 100%, 60% 100%);}
        .tag span{position:relative;z-index:1;padding-left:2px;}
        .tag-upcoming{background:#E4EBF6;color:var(--steel);}
        .tag-overdue{background:var(--danger-bg);color:var(--danger);}
        .tag-completed{background:var(--success-bg);color:var(--success);}
        .tag-pending{background:var(--warning-bg);color:var(--warning);}
        .tag-nocontract{background:#EFE9F7;color:#6B3FA0;}
        .tag-accepted{background:var(--success-bg);color:var(--success);}
        .tag-declined{background:var(--danger-bg);color:var(--danger);}
        .tag-awaiting{background:#F1F1F1;color:#6b6b6b;}
        .tag-expiring{background:var(--warning-bg);color:var(--warning);}
        .tag-active{background:var(--success-bg);color:var(--success);}
        .tag-new{background:#E4EBF6;color:var(--steel);}
        .tag-updated{background:var(--warning-bg);color:var(--warning);}
        .tag-previous{background:#EFE9F7;color:#6B3FA0;}

        /* Tables */
        table.ef-table{width:100%;border-collapse:separate;border-spacing:0;font-size:13px;}
        table.ef-table thead th{
            text-align:left;font-size:10.8px;text-transform:uppercase;letter-spacing:0.7px;
            color:var(--ink-soft);font-weight:600;padding:9px 14px;border-bottom:1px solid var(--line);
            background:#FAFBFD;white-space:nowrap;
        }
        table.ef-table thead th:first-child{border-top-left-radius:10px;}
        table.ef-table thead th:last-child{border-top-right-radius:10px;}
        table.ef-table tbody td{padding:11px 14px;border-bottom:1px solid var(--line);vertical-align:middle;}
        table.ef-table tbody tr:last-child td{border-bottom:none;}
        table.ef-table tbody tr:hover{background:#FAFBFD;}
        .cell-primary{font-weight:600;color:var(--navy);}
        .cell-sub{font-size:11.5px;color:var(--ink-soft);}

        .filter-bar{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:14px;}
        .filter-bar .form-select,.filter-bar .form-control{font-size:12.5px;border-radius:8px;border:1px solid var(--line);padding:7px 10px;}
        .btn-ef-primary{background:var(--orange);border:none;color:#1a1300;font-weight:600;font-size:13px;padding:8px 16px;border-radius:8px;cursor:pointer;}
        .btn-ef-primary:hover{background:#ff5b1f;color:#1a1300;}
        .btn-ef-outline{background:#fff;border:1px solid var(--line);color:var(--steel);font-weight:600;font-size:13px;padding:8px 14px;border-radius:8px;cursor:pointer;}
        .btn-ef-outline:hover{background:var(--paper);}

        /* Pill tabs */
        .pill-tab{padding:6px 14px;border-radius:20px;font-size:12.5px;font-weight:600;color:var(--ink-soft);cursor:pointer;border:1px solid transparent;}
        .pill-tab.active{background:var(--navy);color:#fff;}

        /* Timeline strip */
        .timeline-strip{display:flex;border-radius:10px;overflow:hidden;border:1px solid var(--line);}
        .ts-seg{flex:1;padding:14px 16px;position:relative;}
        .ts-seg + .ts-seg{border-left:1px solid var(--line);}
        .ts-seg .n{font-family:var(--disp);font-size:26px;font-weight:700;color:var(--navy);}
        .ts-seg .lbl{font-size:11px;text-transform:uppercase;letter-spacing:0.7px;color:var(--ink-soft);font-weight:600;}
        .ts-seg .bar{height:5px;border-radius:4px;margin-top:8px;}

        /* Stat strip */
        .stat-strip{display:flex;gap:0;border:1px solid var(--line);border-radius:10px;overflow:hidden;background:#fff;}
        .stat-strip .s{flex:1;padding:12px 16px;text-align:center;}
        .stat-strip .s + .s{border-left:1px solid var(--line);}
        .stat-strip .s b{display:block;font-family:var(--disp);font-size:20px;color:var(--navy);}
        .stat-strip .s span{font-size:10.5px;text-transform:uppercase;color:var(--ink-soft);letter-spacing:0.6px;}

        /* Report card */
        .report-card{border:1px solid var(--line);border-radius:10px;padding:16px;background:var(--card);transition:box-shadow .12s ease, border-color .12s ease;cursor:pointer;}
        .report-card:hover{border-color:var(--orange);box-shadow:0 3px 10px rgba(11,37,69,0.06);}
        .report-card i.rep-icon{font-size:20px;color:var(--orange);}
        .report-card h6{font-size:13.5px;font-weight:700;color:var(--navy);margin:8px 0 3px;}
        .report-card p{font-size:11.5px;color:var(--ink-soft);margin:0;}

        /* Alert panel */
        .alert-panel{
            position:absolute;right:0;top:45px;width:390px;max-width:calc(100vw - 30px);background:#fff;
            border:1px solid var(--line);border-radius:12px;box-shadow:0 14px 40px rgba(11,37,69,.16);
            display:none;overflow:hidden;z-index:100;
        }
        .alert-panel.show{display:block;}
        .alert-head{padding:12px 14px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;}
        .alert-head b{font-family:var(--disp);font-size:18px;}
        .alert-item{padding:12px 14px;border-bottom:1px solid var(--line);display:flex;gap:10px;cursor:pointer;}
        .alert-item:hover{background:#FAFBFD;}
        .alert-item:last-child{border-bottom:0;}
        .alert-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex:none;}
        .alert-icon.warning{background:var(--warning-bg);color:var(--warning);}
        .alert-icon.danger{background:var(--danger-bg);color:var(--danger);}
        .alert-icon.success{background:var(--success-bg);color:var(--success);}
        .alert-icon.info{background:#E4EBF6;color:var(--steel);}
        .alert-text b{font-size:12.5px;display:block;}
        .alert-text span{font-size:11px;color:var(--ink-soft);}
        .alert-unread{width:7px;height:7px;background:var(--orange);border-radius:50%;margin-left:auto;margin-top:5px;flex:none;}

        .report-builder{
          background:#F8FAFD;border:1px dashed #C8D2E2;border-radius:10px;padding:14px;margin-top:14px;
        }
        .report-builder .form-label{font-size:10px;text-transform:uppercase;font-weight:700;color:var(--ink-soft);}

        /* File drop */
        .file-drop{border:1px dashed #B9C7DA;border-radius:9px;padding:14px;text-align:center;background:#FAFBFD;font-size:12px;color:var(--ink-soft);}
        .file-drop i{font-size:24px;color:var(--orange);display:block;margin-bottom:4px;}

        /* Toast */
        .toast-container{z-index:1200;}
        .ef-toast{border:1px solid var(--line);box-shadow:0 10px 30px rgba(11,37,69,.15);border-radius:10px;}

        /* CRUD buttons */
        .crud-actions{display:flex;gap:6px;justify-content:flex-end;}
        .crud-actions .btn{width:32px;height:32px;padding:0;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;}
        .btn-edit{background:#E4EBF6;color:var(--steel);border:0;}
        .btn-delete{background:var(--danger-bg);color:var(--danger);border:0;}
        .btn-view{background:var(--success-bg);color:var(--success);border:0;}

        /* Bootstrap overrides */
        .btn-primary{background:var(--orange);border-color:var(--orange);}
        .btn-primary:hover{background:#ff5b1f;border-color:#ff5b1f;}
        .form-control,.form-select{border-color:var(--line);border-radius:8px;}
        .form-control:focus,.form-select:focus{border-color:var(--orange);box-shadow:0 0 0 0.2rem rgba(255,107,53,0.1);}
        .input-group .form-select{border-top-right-radius:0;border-bottom-right-radius:0;}
        .input-group .btn{border-top-left-radius:0;border-bottom-left-radius:0;border:1px solid var(--line);border-left:none;color:var(--steel);background:#fff;}
        .input-group .btn:hover{background:var(--paper);}
        .alert-success{background-color:var(--success-bg);color:var(--success);border-color:var(--success);}
        .alert-danger{background-color:var(--danger-bg);color:var(--danger);border-color:var(--danger);}
        .badge{font-family:var(--mono);font-size:11px;font-weight:600;padding:4px 9px;}

        .empty-note{padding:40px 20px;text-align:center;color:var(--ink-soft);}
        .empty-note i{font-size:28px;color:var(--line);}

        /* SweetAlert Custom Styling */
        .swal2-eliteflow .swal2-popup {
            border-radius: 12px;
            border: 1px solid var(--line);
            box-shadow: 0 10px 30px rgba(11,37,69,.15);
        }
        .swal2-eliteflow .swal2-title {
            font-family: var(--disp);
            font-size: 22px;
            font-weight: 600;
            color: var(--navy);
        }
        .swal2-eliteflow .swal2-html-container {
            font-size: 14px;
            color: var(--ink);
        }
        .swal2-eliteflow .swal2-confirm {
            background: var(--orange);
            border: none;
            color: #1a1300;
            font-weight: 600;
            font-size: 13px;
            padding: 10px 18px;
            border-radius: 8px;
        }
        .swal2-eliteflow .swal2-confirm:hover {
            background: #ff5b1f;
        }
        .swal2-eliteflow .swal2-cancel {
            background: #fff;
            border: 1px solid var(--line);
            color: var(--steel);
            font-weight: 600;
            font-size: 13px;
            padding: 10px 16px;
            border-radius: 8px;
        }
        .swal2-eliteflow .swal2-cancel:hover {
            background: var(--paper);
        }

        /* Select2 Custom Styling for EliteFlow */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 9px 11px;
            font-size: 13px;
            color: var(--ink);
            background-color: #fff;
        }
        .select2-container--bootstrap-5 .select2-selection--single {
            height: auto;
        }
        .select2-container--bootstrap-5 .select2-selection__rendered {
            padding: 0;
        }
        .select2-container--bootstrap-5 .select2-selection__placeholder {
            color: var(--ink-soft);
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(11,37,69,0.1);
        }
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: var(--orange);
            color: #1a1300;
        }
        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: var(--orange-dim);
            color: var(--navy);
        }
        .select2-container--bootstrap-5 .select2-search__field {
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13px;
        }
        .select2-container--bootstrap-5 .select2-search__field:focus {
            border-color: var(--orange);
            outline: none;
        }

        ::-webkit-scrollbar{width:9px;height:9px;}
        ::-webkit-scrollbar-thumb{background:#c9d2e0;border-radius:6px;}

        @media (max-width:900px){
            #sidebar{position:fixed;left:-230px;z-index:40;transition:left .2s ease;}
            #sidebar.open{left:0;}
            #main{width:100%;}
            #menuBtn{display:flex!important;}
        }
    </style>
</head>
<body>

<div id="shell">
    <!-- SIDEBAR -->
    <div id="sidebar">
        <div class="brand">
            <div class="brand-mark">ET</div>
            <div class="brand-text">
                <div class="top">EliteFlow</div>
                <div class="sub">Elite Tech Precision</div>
            </div>
        </div>
        <div class="nav-container">
            @include('layouts.navigation')
        </div>
        <div class="sidebar-foot">
            EliteFlow v0.1
        </div>
    </div>

    <!-- MAIN -->
    <div id="main">
        <div id="topbar">
            <button class="icon-btn d-none" id="menuBtn" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
            <div class="page-title">{{ isset($pageTitle) ? $pageTitle : 'Dashboard' }}</div>
            <form method="GET" action="{{ route('search') }}" class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search customers, sites, job sheets…" minlength="2" aria-label="Global search">
            </form>
            <div class="topbar-actions">
                <!-- Alert bell -->
                <div class="position-relative" id="alertWrap">
                    <div class="icon-btn" id="alertBtn" title="Alerts">
                        <i class="bi bi-bell-fill"></i>
                        <div class="dot"></div>
                    </div>
                    <div class="alert-panel" id="alertPanel">
                        <div class="alert-head"><b>Alerts & Notifications</b><span class="cell-sub">{{ \App\Models\Contract::where('status','overdue')->count() + \App\Models\Response::where('response','awaiting')->count() }} new</span></div>
                        <div class="alert-item" onclick="location.href='{{ route('contracts.index') }}'">
                            <div class="alert-icon danger"><i class="bi bi-exclamation-triangle"></i></div>
                            <div class="alert-text"><b>{{ \App\Models\Contract::where('status','overdue')->count() }} PPMs are overdue</b><span>Immediate scheduling action required.</span></div><div class="alert-unread"></div>
                        </div>
                        <div class="alert-item" onclick="location.href='{{ route('contracts.index') }}'">
                            <div class="alert-icon warning"><i class="bi bi-calendar-event"></i></div>
                            <div class="alert-text"><b>{{ \App\Models\Contract::where('status','upcoming')->count() }} PPMs due within 30 days</b><span>Customer reminders are ready to send.</span></div><div class="alert-unread"></div>
                        </div>
                        <div class="alert-item" onclick="location.href='{{ route('responses.index') }}'">
                            <div class="alert-icon success"><i class="bi bi-envelope-check"></i></div>
                            <div class="alert-text"><b>{{ \App\Models\Response::where('response','!=','awaiting')->count() }} customer responses received</b><span>Accepted / declined responses need review.</span></div><div class="alert-unread"></div>
                        </div>
                        <div class="alert-item" onclick="location.href='{{ route('customers.index') }}'">
                            <div class="alert-icon info"><i class="bi bi-person-x"></i></div>
                            <div class="alert-text"><b>{{ \App\Models\Customer::where('status','nocontract')->count() }} customers have no contract</b><span>Potential sales follow-up candidates.</span></div>
                        </div>
                    </div>
                </div>

                <div class="icon-btn"><i class="bi bi-question-circle"></i></div>
                <div class="user-chip" id="userChip">
                    <div class="user-ava">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}</div>
                    <div>
                        <div class="name">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="role">Administrator</div>
                    </div>
                </div>
            </div>
        </div>

        <div id="content">
            @if(session('success'))
                <div class="alert alert-success mb-3" role="alert" id="successAlert">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mb-3" role="alert" id="errorAlert">{{ session('error') }}</div>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>

<!-- Toast container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>

<!-- Logout modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:0;overflow:hidden;">
            <div class="modal-header" style="background:var(--navy);color:#fff;border:0;">
                <h5 class="modal-title" style="font-family:var(--disp);font-size:20px;">Sign Out</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <p style="margin:0;">Are you sure you want to sign out of EliteFlow?</p>
            </div>
            <div class="modal-footer" style="border:0;padding:0 20px 16px;">
                <button type="button" class="btn btn-ef-outline" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-ef-primary">Sign Out</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Alert panel toggle
    const alertBtn = document.getElementById('alertBtn');
    const alertPanel = document.getElementById('alertPanel');
    const alertWrap = document.getElementById('alertWrap');

    alertBtn?.addEventListener('click', () => alertPanel.classList.toggle('show'));
    document.addEventListener('click', e => {
        if (alertWrap && !alertWrap.contains(e.target)) alertPanel.classList.remove('show');
    });

    // User chip logout
    document.getElementById('userChip')?.addEventListener('click', () => {
        new bootstrap.Modal(document.getElementById('logoutModal')).show();
    });

    // Show success message with SweetAlert if present (after SweetAlert is loaded)
    document.addEventListener('DOMContentLoaded', function() {
        // Set default SweetAlert configuration for all alerts
        if (typeof Swal !== 'undefined') {
            Swal.defaults.customClass = {
                popup: 'swal2-eliteflow',
                confirmButton: 'btn btn-ef-primary',
                cancelButton: 'btn btn-ef-outline'
            };
            Swal.defaults.buttonsStyling = false;
            Swal.defaults.confirmButtonColor = '#FF6B35';
            Swal.defaults.cancelButtonColor = '#13315C';
        }

        const successAlert = document.getElementById('successAlert');
        if (successAlert && typeof Swal !== 'undefined') {
            const message = successAlert.textContent;
            successAlert.style.display = 'none';
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                confirmButtonText: 'OK'
            });
        }

        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert && typeof Swal !== 'undefined') {
            const message = errorAlert.textContent;
            errorAlert.style.display = 'none';
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonText: 'OK'
            });
        }
    });

    // Initialize Select2 for all dropdowns
    $(document).ready(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            // Only initialize Select2 on form dropdowns, not filter dropdowns
            $('select[name="customer_name"], select[name="area"], select[name="region"], select[name="category"], select[name="engineer_id"], select[name="engineer"], select[name="status"], select[name="service_type"], select[name="frequency"], select[name="response"]').not('.select2-hidden-accessible').each(function() {
                if (!$(this).hasClass('select2-hidden-accessible') && !$(this).closest('.filter-bar').length) {
                    $(this).select2({
                        placeholder: 'Select option',
                        allowClear: true,
                        width: '100%',
                        theme: 'bootstrap-5',
                        dropdownParent: $(document.body),
                        minimumResultsForSearch: 0
                    });
                }
            });
        }
    });
</script>
</body>
</html>
