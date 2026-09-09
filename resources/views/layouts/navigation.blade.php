<div class="nav-group">
    <div class="nav-label">Overview</div>
    <a class="nav-link eliteflow {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill"></i>Dashboard</a>
</div>

<div class="nav-group">
    <div class="nav-label">Operations</div>
    <!-- <a class="nav-link eliteflow {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
        <i class="bi bi-people-fill"></i>Customers
        <span class="badge-count">{{ \App\Models\Customer::count() }}</span>
    </a> -->
    <a class="nav-link eliteflow {{ request()->routeIs('contracts.*') ? 'active' : '' }}" href="{{ route('contracts.index') }}">
        <i class="bi bi-file-earmark-text-fill"></i>Contracts & PPM
        <span class="badge-count">{{ \App\Models\Contract::count() }}</span>
    </a>
    <a class="nav-link eliteflow {{ request()->routeIs('service-history') ? 'active' : '' }}" href="{{ route('service-history') }}"><i class="bi bi-clipboard2-check-fill"></i>Service History</a>
    <!-- <a class="nav-link eliteflow {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}"><i class="bi bi-wrench-adjustable-circle-fill"></i>Service CRUD</a> -->
    <a class="nav-link eliteflow {{ request()->routeIs('data-entry') ? 'active' : '' }}" href="{{ route('data-entry') }}"><i class="bi bi-pencil-square"></i>Manual Data Entry</a>
    <a class="nav-link eliteflow {{ request()->routeIs('responses.*') ? 'active' : '' }}" href="{{ route('responses.index') }}">
        <i class="bi bi-envelope-check-fill"></i>Customer Responses
        <span class="badge-count">{{ \App\Models\Response::where('response', 'awaiting')->count() }}</span>
    </a>
</div>

<div class="nav-group">
    <div class="nav-label">Insights</div>
    @if(auth()->user()?->isAdmin())
        <a class="nav-link eliteflow {{ request()->routeIs('reports') ? 'active' : '' }}" href="{{ route('reports') }}"><i class="bi bi-file-earmark-excel-fill"></i>Reports & Export</a>
    @endif
</div>

<div class="nav-group">
    <div class="nav-label">Administration</div>
    @if(auth()->check())
        <a class="nav-link eliteflow {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-people-fill"></i>User Management</a>
        <a class="nav-link eliteflow {{ request()->routeIs('users.create') ? 'active' : '' }}" href="{{ route('users.create') }}"><i class="bi bi-person-plus-fill"></i>Add User</a>
    @endif
    <a class="nav-link eliteflow {{ request()->routeIs('settings') ? 'active' : '' }}" href="{{ route('settings') }}"><i class="bi bi-gear-fill"></i>Settings</a>
</div>
