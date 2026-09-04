<nav x-data="{ open: false }" style="background:#171514;" class="border-b border-black/20">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" style="font-weight:800;font-size:22px;letter-spacing:-0.06em;color:#fff;">
                        <span style="color:#f40087;">W</span>endee
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" style="display:inline-flex;align-items:center;padding:0 4px;border-bottom:2px solid {{ request()->routeIs('dashboard') ? '#f40087' : 'transparent' }};color:{{ request()->routeIs('dashboard') ? '#fff' : '#a8a29a' }};font-size:13px;font-weight:600;text-decoration:none;">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('cabinets.index') }}" style="display:inline-flex;align-items:center;padding:0 4px;border-bottom:2px solid {{ request()->routeIs('cabinets.index') ? '#f40087' : 'transparent' }};color:{{ request()->routeIs('cabinets.index') ? '#fff' : '#a8a29a' }};font-size:13px;font-weight:600;text-decoration:none;">
                        {{ __('Cabinets') }}
                    </a>
                    <a href="{{ route('cabinets.create') }}" style="display:inline-flex;align-items:center;padding:0 4px;border-bottom:2px solid {{ request()->routeIs('cabinets.create') ? '#f40087' : 'transparent' }};color:{{ request()->routeIs('cabinets.create') ? '#fff' : '#a8a29a' }};font-size:13px;font-weight:600;text-decoration:none;">
                        {{ __('Créer un cabinet') }}
                    </a>
                    <a href="{{ route('a-faire.index') }}" style="display:inline-flex;align-items:center;padding:0 4px;border-bottom:2px solid {{ request()->routeIs('a-faire.*') ? '#f40087' : 'transparent' }};color:{{ request()->routeIs('a-faire.*') ? '#fff' : '#a8a29a' }};font-size:13px;font-weight:600;text-decoration:none;">
                        {{ __('À faire') }}
                    </a>
                    <a href="{{ route('mails.index') }}" style="display:inline-flex;align-items:center;padding:0 4px;border-bottom:2px solid {{ request()->routeIs('mails.*') ? '#f40087' : 'transparent' }};color:{{ request()->routeIs('mails.*') ? '#fff' : '#a8a29a' }};font-size:13px;font-weight:600;text-decoration:none;">
                        {{ __('Mails') }}
                    </a>
                    <a href="{{ route('comptes.create') }}" style="display:inline-flex;align-items:center;padding:0 4px;border-bottom:2px solid {{ request()->routeIs('comptes.*') ? '#f40087' : 'transparent' }};color:{{ request()->routeIs('comptes.*') ? '#fff' : '#a8a29a' }};font-size:13px;font-weight:600;text-decoration:none;">
                        {{ __('Créer un compte') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button style="color:#fff;" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-transparent hover:opacity-80 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cabinets.index')" :active="request()->routeIs('cabinets.index')">
                {{ __('Cabinets') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cabinets.create')" :active="request()->routeIs('cabinets.create')">
                {{ __('Créer un cabinet') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('a-faire.index')" :active="request()->routeIs('a-faire.*')">
                {{ __('À faire') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('mails.index')" :active="request()->routeIs('mails.*')">
                {{ __('Mails') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('comptes.create')" :active="request()->routeIs('comptes.*')">
                {{ __('Créer un compte') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
