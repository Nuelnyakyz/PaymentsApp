<aside class="hidden lg:flex lg:flex-col lg:w-64 lg:shrink-0 lg:h-screen lg:sticky lg:top-0 bg-white border-r border-gray-200">
    <div class="h-16 px-4 border-b border-gray-100"></div>
    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">
        @auth
            @if (Auth::user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'text-secondary bg-gray-50' : 'text-primary hover:text-secondary' }}">{{ __('nav.admin_dashboard') }}</a>
                <a href="{{ route('admin.payments.index') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 {{ request()->routeIs('admin.payments.index') ? 'text-secondary bg-gray-50' : 'text-primary hover:text-secondary' }}">{{ __('nav.payments') }}</a>
                <a href="{{ route('admin.receipts.index') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 {{ request()->routeIs('admin.receipts.index') ? 'text-secondary bg-gray-50' : 'text-primary hover:text-secondary' }}">{{ __('nav.receipts') }}</a>
                <a href="{{ route('admin.client-apps.index') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 {{ request()->routeIs('admin.client-apps.*') ? 'text-secondary bg-gray-50' : 'text-primary hover:text-secondary' }}">Client Apps</a>
                <a href="{{ route('admin.services.index') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 {{ request()->routeIs('admin.services.*') ? 'text-secondary bg-gray-50' : 'text-primary hover:text-secondary' }}">Services</a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 {{ request()->routeIs('admin.users.*') ? 'text-secondary bg-gray-50' : 'text-primary hover:text-secondary' }}">{{ __('nav.users') }}</a>
            @endif
        @endauth
    </nav>
</aside>
