<nav class="bg-slate-950 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- Left: brand --}}
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="text-sky-400 font-semibold tracking-wide">
                    {{ \App\Helpers\RaynetSetting::groupName() }}
                </a>
            </div>

            {{-- Centre: main nav --}}
            <div class="hidden md:flex space-x-6 text-sm">
                <a href="{{ route('home') }}" class="hover:text-sky-300">Home</a>
                <a href="{{ route('about') }}" class="hover:text-sky-300">About</a>
                <a href="{{ route('event-support') }}" class="hover:text-sky-300">Event support</a>
                <a href="{{ route('training') }}" class="hover:text-sky-300">Training</a>
                <a href="{{ route('events.index') }}" class="hover:text-sky-300">Events</a>
            </div>

            {{-- Right: auth / admin pills --}}
            <div class="flex items-center space-x-3 text-sm">
                @auth
                    {{-- Members’ hub pill --}}
                    <a href="{{ route('members') }}"
                       class="px-3 py-1 rounded-full bg-sky-600 hover:bg-sky-500 text-white">
                        Members’ hub
                    </a>

                    {{-- If this user is an admin, show admin dashboard pill --}}
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="px-3 py-1 rounded-full bg-purple-600 hover:bg-purple-500 text-white">
                            Admin dashboard
                        </a>
                    @else
                        {{-- Optional: keep an admin login link even when logged in as non-admin --}}
                        <a href="{{ route('admin.login') }}"
                           class="px-3 py-1 rounded-full border border-slate-500 hover:bg-slate-800">
                            Admin login
                        </a>
                    @endif

                    {{-- Log out --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1 rounded-full border border-slate-500 hover:bg-slate-800">
                            Log out
                        </button>
                    </form>
                @else
                    {{-- Logged out: just show Member login + Admin login --}}
                    <a href="{{ route('login') }}"
                       class="px-3 py-1 rounded-full bg-sky-600 hover:bg-sky-500 text-white">
                        Member login
                    </a>

                    <a href="{{ route('admin.login') }}"
                       class="px-3 py-1 rounded-full border border-slate-500 hover:bg-slate-800">
                        Admin login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>