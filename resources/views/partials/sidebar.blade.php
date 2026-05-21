@php
    $nav = [
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Students', 'route' => 'students.index', 'prefix' => 'students'],
        ['label' => 'Appointments', 'route' => 'appointments.index', 'prefix' => 'appointments'],
        ['label' => 'Counselors', 'route' => 'counselors.index', 'prefix' => 'counselors'],
        ['label' => 'Reports & Analytics', 'route' => 'reports.index', 'prefix' => 'reports'],
    ];
    $routeName = request()->route()?->getName() ?? '';
@endphp


<aside
    data-sidebar
    class="fixed top-0 left-0 z-50 h-screen w-64 shrink-0 border-r border-white/20 bg-[#140DED] transition-transform duration-200 ease-in-out max-lg:translate-x-full overflow-y-auto"
>
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between border-b border-white/20 p-6">
            <div class="flex items-center gap-3">
                <img 
                src="{{ asset('images/cpc logo.png') }}"
                alt="logo"
                class="flex h-13 w-13 items-center justify-center rounded-lg bg-white/60 text-xl font-bold text-white p-1">
</img>
                <div>
                    <h1 class="text-sm font-bold leading-tight text-white">Cordova Public College</h1>
                    <p class="text-xs text-white/70">Counseling Management</p>
                </div>
            </div>
            <button type="button" data-sidebar-close class="text-white lg:hidden" aria-label="Close menu">&times;</button>
        </div>

        <nav class="flex-1 space-y-1 p-4">
            @foreach ($nav as $item)
                @php
                    $active = isset($item['prefix'])
                        ? str_starts_with($routeName, $item['prefix'].'.')
                        : $routeName === $item['route'];
                @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors {{ $active ? 'text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</aside>
