<!-- SIDEBAR PARTIAL -->
<aside class="w-64 bg-white border-r border-[#E5E7EB] shrink-0 hidden md:flex flex-col">

    <!-- Logo & System Title -->
    <div class="px-5 py-5 border-b border-[#E5E7EB]">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 bg-[#4B6A34] rounded-lg flex items-center justify-center shrink-0">
                <i data-lucide="sprout" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h1 class="font-bold text-sm text-[#18181B] leading-tight">First Resources</h1>
                <p class="text-xs text-[#71717A] font-medium">Academy</p>
            </div>
        </div>

        <!-- Logged-in User Card -->
        <div class="flex items-center gap-3 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl px-3 py-2.5">
            <div class="w-8 h-8 rounded-full bg-[#4B6A34] text-white flex items-center justify-center text-xs font-bold shrink-0">
                AN
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-[#18181B] truncate leading-tight">Admin Name</p>
                <p class="text-[11px] text-[#71717A] font-medium">Administrator</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-5">

        <!-- SECTION: Dashboard -->
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] px-3 mb-1.5">Dashboard</p>
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                <i data-lucide="layout-grid" class="w-4 h-4 shrink-0"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- SECTION: Administration -->
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] px-3 mb-1.5">Administration</p>
            <div class="space-y-0.5">

                <!-- Cost -->
                <a href="{{ route('administrasi.cost.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.cost.*') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="banknote" class="w-4 h-4 shrink-0"></i>
                    <span>Cost</span>
                </a>

                <!-- VRA -->
                <a href="{{ route('administrasi.vra.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.vra.*') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="truck" class="w-4 h-4 shrink-0"></i>
                    <span>VRA</span>
                </a>

                <!-- Biaya Rekruit -->
                <a href="{{ route('administrasi.biaya-rekruit.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.biaya-rekruit.*') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="user-plus" class="w-4 h-4 shrink-0"></i>
                    <span>Biaya Rekruit</span>
                </a>

                <!-- CAPEX -->
                <a href="{{ route('administrasi.capex.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.capex.*') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="landmark" class="w-4 h-4 shrink-0"></i>
                    <span>CAPEX</span>
                </a>

                <!-- Cuti -->
                <a href="{{ route('administrasi.cuti.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.cuti.*') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="calendar-check-2" class="w-4 h-4 shrink-0"></i>
                    <span>Cuti</span>
                </a>

                <!-- MPP -->
                <a href="{{ route('administrasi.mpp.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.mpp.*') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                    <span>MPP</span>
                </a>

                <!-- Monitoring Karyawan -->
                <a href="{{ route('administrasi.monitoring.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring.*') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="user-check" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring</span>
                </a>

                <!-- Inventaris -->
                <a href="{{ route('administrasi.inventaris.summary') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.inventaris.summary') || request()->routeIs('administrasi.inventaris.index') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="archive" class="w-4 h-4 shrink-0"></i>
                    <span>Inventaris</span>
                </a>
            </div>
        </div>

        <!-- SECTION: Data Aset -->
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] px-3 mb-1.5">Data Aset</p>
            <div class="space-y-0.5">
                <!-- Gudang -->
                <a href="{{ route('administrasi.inventaris.gudang.barang') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.inventaris.gudang.*') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="warehouse" class="w-4 h-4 shrink-0"></i>
                    <span>Gudang</span>
                </a>

                <!-- Kantor -->
                <a href="{{ route('administrasi.inventaris.kantor.barang') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.inventaris.kantor.*') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="building-2" class="w-4 h-4 shrink-0"></i>
                    <span>Kantor</span>
                </a>

                <!-- Mess -->
                <a href="{{ route('administrasi.inventaris.mess.barang') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.inventaris.mess.*') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="hotel" class="w-4 h-4 shrink-0"></i>
                    <span>Mess</span>
                </a>
            </div>
        </div>

        <!-- SECTION: Kalender Training (existing route) -->
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] px-3 mb-1.5">Program</p>
            <a href="{{ route('kalender-training.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('kalender-training.*') ? 'bg-[#4B6A34] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                <i data-lucide="calendar" class="w-4 h-4 shrink-0"></i>
                <span>Kalender Training</span>
            </a>
        </div>
    </nav>

    <!-- Sidebar Footer: Settings + Logout -->
    <div class="px-3 pb-4 border-t border-[#E5E7EB] pt-3 space-y-0.5">
        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium transition-colors">
            <i data-lucide="settings" class="w-4 h-4 shrink-0"></i>
            <span>Settings</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-500 hover:bg-red-50 hover:text-red-600 font-medium transition-colors">
            <i data-lucide="log-out" class="w-4 h-4 shrink-0"></i>
            <span>Logout</span>
        </a>
        <p class="text-center text-[10px] text-[#A1A1AA] pt-2">v{{ app()->version() }} &copy; FR Academy {{ date('Y') }}</p>
    </div>
</aside>
