<nav class="bg-white border-b border-gray-100">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left: Brand only -->
            <a href="{{ (Auth::check() && Auth::user()->is_admin) ? route('admin.dashboard') : url('/') }}" class="shrink-0">
                <div class="brand flex items-center gap-2 sm:gap-2.5 group">
                    <img src="{{ asset('Snapay.png') }}" alt="Snapay Logo" class="h-6 w-6 sm:h-7 sm:w-7 object-contain drop-shadow shrink-0" />
                    <span class="text-primary text-base sm:text-lg font-semibold tracking-wide uppercase transition-colors duration-200 group-hover:text-secondary">Snapay</span>
                </div>
            </a>

            <!-- Right: Profile on large, Hamburger on mobile -->
            <div class="flex items-center gap-2 mr-2 sm:mr-4 lg:mr-6">
                <!-- Mobile sidebar toggle on the right -->
                <button class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/20" @click="$store.ui.mobileSidebar = true" aria-label="Open Sidebar">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Profile dropdown on far right for large screens -->
                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-2 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 hover:text-gray-800 bg-white hover:bg-gray-100 focus:outline-none transition">
                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a5 5 0 00-3.535 8.535A7 7 0 003 17a1 1 0 001 1h12a1 1 0 001-1 7 7 0 00-3.465-5.965A5 5 0 0010 2z" clip-rule="evenodd"/></svg>
                                </div>
                                <span class="hidden md:inline text-secondary">{{ Auth::user()->name }}</span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('nav.profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('nav.logout') }}</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>
</nav>
