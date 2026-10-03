<x-admin-layout>
    <x-slot name="title">Dashboard Resignation Staff | FR Academy</x-slot>
    
    <!-- Include ApexCharts for later -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <div class="p-6 bg-[#F8FAFC] min-h-screen" x-data="{ activeTab: '{{ session('success') || session('error') || $errors->any() ? 'data' : 'dashboard' }}', showModal: false, showImportModal: false, showDetailModal: false, showEditModal: false, selectedStaff: {} }">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Tabs -->
            <div class="flex items-center gap-2 mb-6">
                <button @click="activeTab = 'dashboard'" 
                        :class="activeTab === 'dashboard' ? 'bg-[#22C55E] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    Dashboard
                </button>
                <button @click="activeTab = 'data'" 
                        :class="activeTab === 'data' ? 'bg-[#22C55E] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Data Karyawan
                </button>
            </div>

            <!-- TAB: Dashboard -->
            <div x-show="activeTab === 'dashboard'" style="display: none;" x-transition>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Dashboard Resignation Staff</h1>
                            <p class="text-sm text-gray-500">Perbandingan Data Semua Tahun</p>
                        </div>
                        <div class="flex gap-2">
                            <!-- Filter Tahun -->
                            <div x-data="{ openYear: false }" class="relative">
                                <button type="button" @click="openYear = !openYear" class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-medium text-gray-600 bg-white hover:bg-gray-50 flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i> 
                                    @if(count($selectedYears) == count($availableYears)) Semua Tahun @else {{ implode(', ', $selectedYears) }} @endif
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="openYear" @click.away="openYear = false" class="absolute left-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10 p-3" style="display: none;">
                                    <form method="GET" action="{{ route('administrasi.staf-resign.index') }}">
                                        <input type="hidden" name="region" value="{{ request('region') }}">
                                        
                                        <div class="space-y-2">
                                            @foreach($availableYears as $year)
                                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                                <input type="checkbox" name="years[]" value="{{ $year }}" onchange="this.form.submit()" class="rounded text-[#22C55E] focus:ring-[#22C55E]" {{ in_array($year, $selectedYears) ? 'checked' : '' }}>
                                                Tahun {{ $year }}
                                            </label>
                                            @endforeach
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Filter Region -->
                            <div x-data="{ openRegion: false }" class="relative">
                                <button @click="openRegion = !openRegion" class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-medium text-gray-600 bg-white hover:bg-gray-50 flex items-center gap-1.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5"></i> 
                                    {{ request('region') ?: 'All Region' }}
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="openRegion" @click.away="openRegion = false" class="absolute right-0 mt-1 w-32 bg-white border border-gray-200 rounded-lg shadow-lg z-10 py-1" style="display: none;">
                                    <a href="{{ route('administrasi.staf-resign.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-blue-50 {{ !request('region') ? 'bg-blue-100' : '' }}">All Region</a>
                                    <a href="{{ route('administrasi.staf-resign.index', ['region' => 'Kalbar']) }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-blue-50 {{ request('region') == 'Kalbar' ? 'bg-blue-100' : '' }}">Kalbar</a>
                                    <a href="{{ route('administrasi.staf-resign.index', ['region' => 'Kaltim']) }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-blue-50 {{ request('region') == 'Kaltim' ? 'bg-blue-100' : '' }}">Kaltim</a>
                                    <a href="{{ route('administrasi.staf-resign.index', ['region' => 'Riau']) }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-blue-50 {{ request('region') == 'Riau' ? 'bg-blue-100' : '' }}">Riau</a>
                                </div>
                            </div>
                            
                            <a href="{{ route('administrasi.staf-resign.index') }}" class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-medium text-gray-600 bg-white hover:bg-gray-50 flex items-center gap-1.5"><i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Reset Filter</a>
                        </div>
                    </div>
                </div>
                
                <!-- Top Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <!-- Total Resign -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="text-xs text-gray-500 font-semibold uppercase mb-1">Total Resign</div>
                        <div class="text-3xl font-bold text-gray-900 mb-3">{{ $statTotal->total }}</div>
                        <div class="flex items-center gap-4 text-xs border-t border-gray-100 pt-3 overflow-x-auto pb-1">
                            @foreach($selectedYears as $y)
                            @php $statY = $stats->get($y) ?? (object)['total'=>0]; @endphp
                            <div class="min-w-fit">
                                <div class="text-gray-400">Tahun {{ $y }}</div>
                                <div class="font-bold text-gray-800">{{ $statY->total }} <span class="text-gray-400 font-normal">({{ $statTotal->total > 0 ? $calcPct($statY->total, $statTotal->total) : 0 }}%)</span></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Voluntary -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-t-4 border-t-blue-500">
                        <div class="flex justify-between items-end mb-1">
                            <div class="text-xs text-gray-500 font-semibold uppercase">Voluntary (VT)</div>
                            <div class="text-xs font-bold text-blue-500">{{ $calcPct($statTotal->total_vt, $statTotal->total) }}%</div>
                        </div>
                        <div class="text-3xl font-bold text-blue-600 mb-3">{{ $statTotal->total_vt }}</div>
                        <div class="flex items-center gap-4 text-xs border-t border-gray-100 pt-3 overflow-x-auto pb-1">
                            @foreach($selectedYears as $y)
                            @php $statY = $stats->get($y) ?? (object)['total_vt'=>0]; @endphp
                            <div class="min-w-fit">
                                <div class="text-gray-400">Tahun {{ $y }}</div>
                                <div class="font-bold text-gray-800">{{ $statY->total_vt }} <span class="text-gray-400 font-normal">({{ $statTotal->total_vt > 0 ? $calcPct($statY->total_vt, $statTotal->total_vt) : 0 }}%)</span></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Involuntary -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-t-4 border-t-red-500">
                        <div class="flex justify-between items-end mb-1">
                            <div class="text-xs text-gray-500 font-semibold uppercase">Involuntary (IT)</div>
                            <div class="text-xs font-bold text-red-500">{{ $calcPct($statTotal->total_it, $statTotal->total) }}%</div>
                        </div>
                        <div class="text-3xl font-bold text-red-600 mb-3">{{ $statTotal->total_it }}</div>
                        <div class="flex items-center gap-4 text-xs border-t border-gray-100 pt-3 overflow-x-auto pb-1">
                            @foreach($selectedYears as $y)
                            @php $statY = $stats->get($y) ?? (object)['total_it'=>0]; @endphp
                            <div class="min-w-fit">
                                <div class="text-gray-400">Tahun {{ $y }}</div>
                                <div class="font-bold text-gray-800">{{ $statY->total_it }} <span class="text-gray-400 font-normal">({{ $statTotal->total_it > 0 ? $calcPct($statY->total_it, $statTotal->total_it) : 0 }}%)</span></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Alumni -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-t-4 border-t-green-500">
                        <div class="flex justify-between items-end mb-1">
                            <div class="text-xs text-gray-500 font-semibold uppercase">Alumni FRLC</div>
                            <div class="text-xs font-bold text-green-500">{{ $calcPct($statTotal->total_alumni, $statTotal->total) }}%</div>
                        </div>
                        <div class="text-3xl font-bold text-green-600 mb-3">{{ $statTotal->total_alumni }}</div>
                        <div class="flex items-center gap-4 text-xs border-t border-gray-100 pt-3 overflow-x-auto pb-1">
                            @foreach($selectedYears as $y)
                            @php $statY = $stats->get($y) ?? (object)['total_alumni'=>0]; @endphp
                            <div class="min-w-fit">
                                <div class="text-gray-400">Tahun {{ $y }}</div>
                                <div class="font-bold text-gray-800">{{ $statY->total_alumni }} <span class="text-gray-400 font-normal">({{ $statTotal->total_alumni > 0 ? $calcPct($statY->total_alumni, $statTotal->total_alumni) : 0 }}%)</span></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Non Alumni -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-t-4 border-t-orange-500">
                        <div class="flex justify-between items-end mb-1">
                            <div class="text-xs text-gray-500 font-semibold uppercase">Non Alumni</div>
                            <div class="text-xs font-bold text-orange-500">{{ $calcPct($statTotal->total_non_alumni, $statTotal->total) }}%</div>
                        </div>
                        <div class="text-3xl font-bold text-orange-600 mb-3">{{ $statTotal->total_non_alumni }}</div>
                        <div class="flex items-center gap-4 text-xs border-t border-gray-100 pt-3 overflow-x-auto pb-1">
                            @foreach($selectedYears as $y)
                            @php $statY = $stats->get($y) ?? (object)['total_non_alumni'=>0]; @endphp
                            <div class="min-w-fit">
                                <div class="text-gray-400">Tahun {{ $y }}</div>
                                <div class="font-bold text-gray-800">{{ $statY->total_non_alumni }} <span class="text-gray-400 font-normal">({{ $statTotal->total_non_alumni > 0 ? $calcPct($statY->total_non_alumni, $statTotal->total_non_alumni) : 0 }}%)</span></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Charts Row 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-1">Cluster Penyebab Resign</h3>
                        <p class="text-xs text-gray-500 mb-4">Sebaran top cluster 2025 vs 2026</p>
                        <div id="chart-cluster"></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-1">Cluster Resign by Jabatan</h3>
                        <p class="text-xs text-gray-500 mb-4">Top jabatan 2025 vs 2026</p>
                        <div id="chart-jabatan"></div>
                    </div>
                </div>

                <!-- Tables Row 2 -->
                <div class="grid grid-cols-1 {{ count($selectedYears) > 1 ? 'md:grid-cols-2' : '' }} gap-4 mb-6">
                    @foreach($selectedYears as $year)
                    @php 
                        $kTable = $topKebunTables[$year] ?? collect(); 
                        $sumY = $kTable->sum('total'); 
                        $statY = $stats->get($year) ?? (object)['total'=>0]; 
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-900">Sebaran Top Ten Per Kebun - Th. {{ $year }}</h3>
                        </div>
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-4 py-2">KEBUN</th>
                                    <th class="px-4 py-2 text-center">IT</th>
                                    <th class="px-4 py-2 text-center">VT</th>
                                    <th class="px-4 py-2 text-center">TTL</th>
                                    <th class="px-4 py-2 text-center">%</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($kTable as $k)
                                <tr>
                                    <td class="px-4 py-2 font-medium">{{ $k->kebun }}</td>
                                    <td class="px-4 py-2 text-center">{{ $k->it ?: '-' }}</td>
                                    <td class="px-4 py-2 text-center">{{ $k->vt ?: '-' }}</td>
                                    <td class="px-4 py-2 text-center font-bold">{{ $k->total }}</td>
                                    <td class="px-4 py-2 text-center text-orange-500 font-medium">{{ $statY->total > 0 ? $calcPct($k->total, $statY->total) : 0 }}%</td>
                                </tr>
                                @endforeach
                                <tr class="bg-blue-50/50 font-bold text-blue-700">
                                    <td class="px-4 py-3">TOTAL</td>
                                    <td class="px-4 py-3 text-center">{{ $kTable->sum('it') }}</td>
                                    <td class="px-4 py-3 text-center">{{ $kTable->sum('vt') }}</td>
                                    <td class="px-4 py-3 text-center">{{ $sumY }}</td>
                                    <td class="px-4 py-3 text-center">{{ $statY->total > 0 ? $calcPct($sumY, $statY->total) : 0 }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>

                <!-- Table Analisis Kategori Cluster Resign -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900">Analisis Kategori Cluster Resign</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-5 py-3" rowspan="2">KATEGORI CLUSTER</th>
                                    @foreach($selectedYears as $year)
                                    <th class="px-5 py-2 text-center border-b border-gray-200" colspan="2">{{ $year }}</th>
                                    @endforeach
                                    <th class="px-5 py-3" rowspan="2">KETERANGAN</th>
                                </tr>
                                <tr>
                                    @foreach($selectedYears as $year)
                                    <th class="px-5 py-2 text-center">JML (ORG)</th>
                                    <th class="px-5 py-2 text-center">%</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($clusterStats as $c)
                                <tr>
                                    <td class="px-5 py-3 font-medium">{{ $c->cluster_resign }}</td>
                                    @foreach($selectedYears as $year)
                                    @php 
                                        $countVar = 'count_'.$year; 
                                        $countVal = $c->$countVar ?: 0; 
                                        $statY = $stats->get($year) ?? (object)['total'=>0]; 
                                    @endphp
                                    <td class="px-5 py-3 text-center">{{ $countVal ?: '-' }}</td>
                                    <td class="px-5 py-3 text-center">{{ $statY->total > 0 ? $calcPct($countVal, $statY->total) : 0 }}%</td>
                                    @endforeach
                                    <td class="px-5 py-3 text-gray-500 truncate max-w-xs" title="{{ $c->keterangan_cluster }}">{{ $c->keterangan_cluster ?: '-' }}</td>
                                </tr>
                                @endforeach
                                <tr class="bg-gray-50 font-bold text-gray-900">
                                    <td class="px-5 py-3">TOTAL</td>
                                    @foreach($selectedYears as $year)
                                    @php $countVar = 'count_'.$year; @endphp
                                    <td class="px-5 py-3 text-center text-blue-600">{{ $clusterStats->sum($countVar) }}</td>
                                    <td class="px-5 py-3 text-center">100%</td>
                                    @endforeach
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Trend Chart -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-1">Grafik Trend Turnover 2025 - Juli 2026</h3>
                        <div id="chart-trend"></div>
                    </div>
                    <!-- AI Insight -->
                    <div class="bg-indigo-50/50 rounded-xl shadow-sm border border-indigo-100 p-5 flex flex-col h-full items-center justify-center min-h-[250px]">
                        <div class="text-center text-indigo-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            <h3 class="text-sm font-bold text-indigo-900 mb-1">AI Insight & Analisis Pola</h3>
                            <p class="text-xs text-indigo-500">Fitur ini sedang dikosongkan sementara.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: Data Karyawan -->
            <div x-show="activeTab === 'data'" x-transition>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Manajemen Data Karyawan</h2>
                            <p class="text-sm text-gray-500">Kelola data resign karyawan (staff_resign)</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <form method="GET" action="{{ route('administrasi.staf-resign.index') }}" class="relative w-64">
                                <input type="hidden" name="region" value="{{ request('region') }}">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK, Nama, Jabatan..." 
                                       class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 text-sm">
                            </form>
                            <button type="button" @click="showImportModal = true" class="flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors bg-white shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import Data / Template
                            </button>
                            <button type="button" @click="showModal = true" class="flex items-center gap-2 px-4 py-2 bg-[#22C55E] text-white text-sm font-semibold rounded-lg hover:bg-green-600 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah Data
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-white border-b border-gray-100 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-5 py-4 whitespace-nowrap">NIK</th>
                                    <th class="px-5 py-4 whitespace-nowrap">NAMA</th>
                                    <th class="px-5 py-4 whitespace-nowrap">JABATAN</th>
                                    <th class="px-5 py-4 whitespace-nowrap">KEBUN/REGION</th>
                                    <th class="px-5 py-4 whitespace-nowrap text-center">JENIS</th>
                                    <th class="px-5 py-4 whitespace-nowrap">CLUSTER</th>
                                    <th class="px-5 py-4 whitespace-nowrap text-right">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
                                @forelse($resignedStaffs as $staff)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-3 font-medium text-gray-900 text-xs">{{ $staff->employee_id }}</td>
                                        <td class="px-5 py-3 text-gray-800 text-xs font-semibold">{{ $staff->nama }}</td>
                                        <td class="px-5 py-3 text-gray-600 text-xs">{{ $staff->jabatan ?: '-' }}</td>
                                        <td class="px-5 py-3 text-xs">
                                            <div class="text-gray-800 font-medium">{{ $staff->kebun ?: '-' }}</div>
                                            <div class="text-gray-400 mt-0.5">{{ $staff->region ?: '-' }}</div>
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            @if(strtolower($staff->jenis_resign) == 'voluntary')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wide">Voluntary</span>
                                            @elseif(strtolower($staff->jenis_resign) == 'involuntary')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-100 uppercase tracking-wide">Involuntary</span>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-gray-600 text-xs">{{ $staff->cluster_resign ?: '-' }}</td>
                                        <td class="px-5 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button @click="selectedStaff = {{ json_encode($staff) }}; showDetailModal = true" class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </button>
                                                <button @click="selectedStaff = {{ json_encode($staff) }}; showEditModal = true" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <form action="{{ route('administrasi.staf-resign.destroy', $staff->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="h-10 w-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                                <p class="text-sm">Belum ada data karyawan resign.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100 bg-white">
                        {{ $resignedStaffs->links('Administrasi.Pages.Monitoring.pagination') }}
                    </div>
                </div>
            </div>
            
            <!-- Modal Tambah Data Karyawan -->
            <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                    <!-- Overlay -->
                    <div x-show="showModal" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" 
                         class="fixed inset-0" style="background-color: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"
                         @click="showModal = false"></div>
                    
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <!-- Modal Panel -->
                    <div x-show="showModal" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-100">
                        
                        <form action="{{ route('administrasi.staf-resign.store') }}" method="POST">
                            @csrf
                            <div class="bg-white px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Tambah Data Karyawan</h3>
                                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="px-6 py-6 bg-gray-50">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">NIK (Employee ID) <span class="text-red-500">*</span></label>
                                        <input type="text" name="employee_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                                        <input type="text" name="nama" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jabatan</label>
                                        <input type="text" name="jabatan" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kebun</label>
                                        <input type="text" name="kebun" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Region</label>
                                        <input type="text" name="region" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Resign</label>
                                        <select name="jenis_resign" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm h-10 px-3 bg-white">
                                            <option value="Voluntary">Voluntary (VT)</option>
                                            <option value="Involuntary">Involuntary (IT)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Cluster Resign</label>
                                        <input type="text" name="cluster_resign" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status Alumni FRLC</label>
                                        <select name="alumni" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm h-10 px-3 bg-white">
                                            <option value="Non Alumni">Non Alumni</option>
                                            <option value="Alumni">Alumni</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Bergabung</label>
                                        <input type="date" name="join_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm h-10 text-gray-500 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Resign</label>
                                        <input type="date" name="resign_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm h-10 text-gray-500 px-3">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi Resign</label>
                                        <textarea name="deskripsi_resign" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm px-3 py-2"></textarea>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan Tambahan</label>
                                        <input type="text" name="keterangan" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm h-10 px-3">
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-end gap-3 rounded-b-xl">
                                <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-[#10B981] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-600 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Import Data -->
            <div x-show="showImportModal" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                    <!-- Overlay -->
                    <div x-show="showImportModal" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" 
                         class="fixed inset-0" style="background-color: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"
                         @click="showImportModal = false"></div>
                    
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <!-- Modal Panel -->
                    <div x-show="showImportModal" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100">
                        
                        <div class="bg-white px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Import Data</h3>
                            <button type="button" @click="showImportModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="px-6 py-6 bg-white">
                            <p class="text-sm text-gray-600 mb-6 leading-relaxed">Anda dapat menambahkan banyak data sekaligus dengan mengunggah file. Unduh template di bawah ini untuk melihat format kolom yang dibutuhkan.</p>
                            
                            <a href="{{ route('administrasi.staf-resign.template') }}" class="w-full flex justify-center items-center gap-2 px-4 py-3 border border-blue-200 bg-blue-50 text-blue-600 text-sm font-semibold rounded-lg hover:bg-blue-100 transition-colors mb-6">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Unduh Template
                            </a>

                            <form action="{{ route('administrasi.staf-resign.import-preview') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer" onclick="document.getElementById('file-upload').click()">
                                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <span class="block text-sm font-semibold text-gray-700 mb-1">Klik untuk Unggah File</span>
                                    <span class="block text-xs text-gray-500">Mendukung format .xlsx, .xls, dan .csv</span>
                                    <input id="file-upload" name="file" type="file" class="hidden" accept=".xlsx,.xls,.csv" onchange="this.form.submit()">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Detail Modal -->
                <div x-show="showDetailModal" class="fixed inset-0 z-[110] bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
                    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden" @click.away="showDetailModal = false">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Detail Karyawan</h3>
                            <button type="button" @click="showDetailModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div><span class="block text-xs font-semibold text-gray-500">NIK</span><span class="text-sm font-medium" x-text="selectedStaff.employee_id || '-'"></span></div>
                                <div><span class="block text-xs font-semibold text-gray-500">Nama</span><span class="text-sm font-medium" x-text="selectedStaff.nama || '-'"></span></div>
                                <div><span class="block text-xs font-semibold text-gray-500">Jabatan</span><span class="text-sm" x-text="selectedStaff.jabatan || '-'"></span></div>
                                <div><span class="block text-xs font-semibold text-gray-500">Kebun / Region</span><span class="text-sm" x-text="(selectedStaff.kebun || '-') + ' / ' + (selectedStaff.region || '-')"></span></div>
                                <div><span class="block text-xs font-semibold text-gray-500">Tanggal Bergabung</span><span class="text-sm" x-text="selectedStaff.join_date || '-'"></span></div>
                                <div><span class="block text-xs font-semibold text-gray-500">Tanggal Resign</span><span class="text-sm" x-text="selectedStaff.resign_date || '-'"></span></div>
                                <div><span class="block text-xs font-semibold text-gray-500">Jenis Resign</span><span class="text-sm font-bold uppercase" :class="selectedStaff.jenis_resign == 'Voluntary' ? 'text-blue-600' : 'text-red-600'" x-text="selectedStaff.jenis_resign || '-'"></span></div>
                                <div><span class="block text-xs font-semibold text-gray-500">Cluster Resign</span><span class="text-sm" x-text="selectedStaff.cluster_resign || '-'"></span></div>
                                <div class="col-span-2"><span class="block text-xs font-semibold text-gray-500">Deskripsi Resign</span><span class="text-sm" x-text="selectedStaff.deskripsi_resign || '-'"></span></div>
                                <div class="col-span-2"><span class="block text-xs font-semibold text-gray-500">Keterangan Tambahan</span><span class="text-sm" x-text="selectedStaff.keterangan || '-'"></span></div>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 flex justify-end">
                            <button type="button" @click="showDetailModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Tutup</button>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div x-show="showEditModal" class="fixed inset-0 z-[110] bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;"
                    x-effect="if(showEditModal && selectedStaff && selectedStaff.id) { document.getElementById('edit-form').action = '{{ url('administrasi/staf-resign') }}/' + selectedStaff.id; }">
                    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden" @click.away="showEditModal = false">
                        <form id="edit-form" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="text-lg font-bold text-gray-900">Edit Data Karyawan</h3>
                                <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div class="px-6 py-6 bg-gray-50">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
                                        <input type="text" name="employee_id" x-model="selectedStaff.employee_id" required class="w-full rounded-lg border-gray-300 shadow-sm sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                                        <input type="text" name="nama" x-model="selectedStaff.nama" required class="w-full rounded-lg border-gray-300 shadow-sm sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jabatan</label>
                                        <input type="text" name="jabatan" x-model="selectedStaff.jabatan" class="w-full rounded-lg border-gray-300 shadow-sm sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kebun</label>
                                        <input type="text" name="kebun" x-model="selectedStaff.kebun" class="w-full rounded-lg border-gray-300 shadow-sm sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Region</label>
                                        <input type="text" name="region" x-model="selectedStaff.region" class="w-full rounded-lg border-gray-300 shadow-sm sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Resign</label>
                                        <select name="jenis_resign" x-model="selectedStaff.jenis_resign" class="w-full rounded-lg border-gray-300 shadow-sm sm:text-sm h-10 px-3">
                                            <option value="Voluntary">Voluntary (VT)</option>
                                            <option value="Involuntary">Involuntary (IT)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Cluster Resign</label>
                                        <input type="text" name="cluster_resign" x-model="selectedStaff.cluster_resign" class="w-full rounded-lg border-gray-300 shadow-sm sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Bergabung</label>
                                        <input type="date" name="join_date" x-model="selectedStaff.join_date" class="w-full rounded-lg border-gray-300 shadow-sm sm:text-sm h-10 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Resign</label>
                                        <input type="date" name="resign_date" x-model="selectedStaff.resign_date" class="w-full rounded-lg border-gray-300 shadow-sm sm:text-sm h-10 px-3">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi Resign</label>
                                        <textarea name="deskripsi_resign" x-model="selectedStaff.deskripsi_resign" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm sm:text-sm px-3 py-2"></textarea>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan Tambahan</label>
                                        <input type="text" name="keterangan" x-model="selectedStaff.keterangan" class="w-full rounded-lg border-gray-300 shadow-sm sm:text-sm h-10 px-3">
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                                <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-[#10B981] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-600">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

        </div>
    </div>

    <!-- ApexCharts Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var chartColors = ['#f97316', '#3b82f6', '#10b981', '#f43f5e', '#8b5cf6', '#eab308'];
            // Cluster Chart (Horizontal Bar)
            var clusterOptions = {
                series: [
                    @foreach($selectedYears as $year)
                    {
                        name: 'Tahun {{ $year }}',
                        data: {!! json_encode($clusterStats->pluck('count_'.$year)) !!}
                    },
                    @endforeach
                ],
                chart: { type: 'bar', height: 350, toolbar: { show: false } },
                plotOptions: { 
                    bar: { 
                        horizontal: true,
                        barHeight: '75%'
                    } 
                },
                dataLabels: { 
                    enabled: false
                },
                xaxis: { 
                    categories: {!! json_encode($clusterStats->pluck('cluster_resign')) !!},
                    labels: { style: { colors: '#6b7280', fontSize: '11px' } }
                },
                yaxis: {
                    labels: { style: { colors: '#374151', fontSize: '11px', fontWeight: 500 } }
                },
                colors: chartColors.slice(0, {{ count($selectedYears) }}),
                legend: { 
                    position: 'bottom',
                    markers: { radius: 12 }, // circle
                    itemMargin: { horizontal: 10, vertical: 5 }
                },
                grid: {
                    borderColor: '#f3f4f6',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: false } }
                }
            };
            new ApexCharts(document.querySelector("#chart-cluster"), clusterOptions).render();

            // Jabatan Chart (Vertical Stacked Column)
            var jabatanOptions = {
                series: [
                    @foreach($selectedYears as $year)
                    {
                        name: 'Tahun {{ $year }}',
                        data: {!! json_encode($jabatanStats->pluck('count_'.$year)) !!}
                    },
                    @endforeach
                ],
                chart: { type: 'bar', height: 350, stacked: true, toolbar: { show: false } },
                plotOptions: {
                    bar: {
                        columnWidth: '55%'
                    }
                },
                dataLabels: { 
                    enabled: false
                },
                xaxis: { 
                    categories: {!! json_encode($jabatanStats->pluck('jabatan')) !!}.map(l => l ? l.split(' ') : ''),
                    labels: { 
                        rotate: 0,
                        style: { colors: '#374151', fontSize: '10px', fontWeight: 500 },
                        maxHeight: 120
                    }
                },
                colors: chartColors.slice(0, {{ count($selectedYears) }}),
                legend: { 
                    position: 'bottom',
                    markers: { radius: 12 }, // circle
                    itemMargin: { horizontal: 10, vertical: 5 }
                },
                grid: {
                    borderColor: '#f3f4f6',
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } }
                }
            };
            new ApexCharts(document.querySelector("#chart-jabatan"), jabatanOptions).render();

            // Trend Chart
            var trendOptions = {
                series: [{
                    name: 'IT',
                    data: {!! json_encode($trendStats->pluck('it')) !!}
                }, {
                    name: 'VT',
                    data: {!! json_encode($trendStats->pluck('vt')) !!}
                }, {
                    name: 'Total',
                    data: {!! json_encode($trendStats->pluck('total')) !!}
                }],
                chart: { type: 'line', height: 350, curve: 'smooth', toolbar: { show: false } },
                stroke: { width: [2, 2, 3], curve: 'smooth' },
                xaxis: { categories: {!! json_encode($trendStats->pluck('month_year')) !!} },
                colors: ['#ef4444', '#22c55e', '#3b82f6'], // red, green, blue
                legend: { position: 'bottom' }
            };
            new ApexCharts(document.querySelector("#chart-trend"), trendOptions).render();
        });
    </script>
    <!-- Success Popup -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms class="fixed bottom-4 right-4 bg-[#10B981] text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 z-[200]" x-init="setTimeout(() => show = false, 3000)">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="font-semibold text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Error Popup -->
    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 z-[200]" x-init="setTimeout(() => show = false, 5000)">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-semibold text-sm">{{ session('error') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 z-[200]" x-init="setTimeout(() => show = false, 5000)">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-semibold text-sm">{{ $errors->first() }}</span>
    </div>
    @endif

    <!-- Preview Import Modal -->
    @if(session('preview_data'))
    <div class="fixed inset-0 z-[110] bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Preview Data Import</h3>
                <a href="{{ route('administrasi.staf-resign.index') }}" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-500 mb-4">Menampilkan 3 baris pertama dari total {{ session('total_rows') }} data yang akan di-import.</p>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Nama</th>
                                <th class="px-4 py-2">Jabatan</th>
                                <th class="px-4 py-2">Tgl Join</th>
                                <th class="px-4 py-2">Tgl Resign</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach(session('preview_data') as $row)
                            <tr>
                                <td class="px-4 py-2">{{ $row['employee_id'] ?? '-' }}</td>
                                <td class="px-4 py-2 font-medium">{{ $row['nama'] ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $row['jabatan'] ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $row['join_date'] ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $row['resign_date'] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-2xl">
                <a href="{{ route('administrasi.staf-resign.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <form action="{{ route('administrasi.staf-resign.import-process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="temp_file" value="{{ session('temp_file') }}">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#10B981] border border-transparent rounded-lg hover:bg-emerald-600">Lanjutkan Import</button>
                </form>
            </div>
        </div>
    </div>
    @endif



</x-admin-layout>
