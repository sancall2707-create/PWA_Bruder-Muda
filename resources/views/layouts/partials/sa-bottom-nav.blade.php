<style>
    .sa-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 480px;
        background: #ffffff;
        border-top: 1px solid #E4E7EC;
        display: flex;
        justify-content: space-around;
        padding: 8px 0;
        box-sizing: border-box;
        z-index: 100;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
    }

    .sa-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        color: #667085;
        font-size: 11px;
        font-weight: 600;
        width: 25%;
    }

    .sa-nav-item.active {
        color: #1455C0;
        font-weight: 800;
    }

    .sa-nav-item svg {
        width: 20px;
        height: 20px;
    }
</style>
<nav class="sa-bottom-nav">
    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('superadmin.dashboard') }}" class="sa-nav-item {{ request()->routeIs('superadmin.dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('superadmin.users.index') }}" class="sa-nav-item {{ request()->routeIs('superadmin.users*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        <span>Bruder</span>
    </a>
    <a href="{{ route('superadmin.monitoring.index') }}" class="sa-nav-item {{ request()->routeIs('superadmin.monitoring*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        <span>Monitoring</span>
    </a>
    <a href="{{ route('superadmin.sapaan.index') }}" class="sa-nav-item {{ request()->routeIs('superadmin.sapaan*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        <span>Sapaan</span>
    </a>
</nav>
