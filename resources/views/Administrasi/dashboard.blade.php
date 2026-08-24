<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Kalender Training | FR Academy Enterprise Portal</title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f4f7f1',
                            100: '#e5ecd9',
                            200: '#cbdba8',
                            300: '#aac577',
                            400: '#87aa4e',
                            500: '#4B6A34', // Brand forest green
                            600: '#3d562a',
                            700: '#2f4321',
                            800: '#222f17',
                            900: '#141c0e',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-[#F8F9FA] text-[#1A1A1A] min-h-screen flex">

    <!-- 1. SIDEBAR -->
    <aside class="w-64 bg-white border-r border-[#E5E7EB] shrink-0 hidden md:flex flex-col">
        <!-- Logo Header -->
        <div class="p-6 border-b border-[#E5E7EB] flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center text-primary-500">
                <i data-lucide="sprout" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="font-bold text-lg text-primary-900 leading-tight">FR Academy</h1>
                <p class="text-xs text-[#71717A]">Enterprise Portal</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
            <!-- Kalender Training Link -->
            <a href="{{ route('kalender-training.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-[#71717A] hover:bg-[#F4F4F5] hover:text-[#18181B] transition-colors">
                <i data-lucide="calendar" class="w-5 h-5"></i>
                <span class="font-medium text-sm">Kalender Training</span>
            </a>
            <!-- Active Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-[#4B6A34]/10 text-primary-500 transition-colors">
                <i data-lucide="layout-dashboard" class="w-5 h-5 text-primary-500"></i>
                <span class="font-semibold text-sm">Dashboard</span>
            </a>
        </nav>
        
        <!-- Sidebar Footer Info -->
        <div class="p-4 border-t border-[#E5E7EB] text-center text-xs text-[#A1A1AA]">
            v{{ app()->version() }} &copy; FR Academy 2026
        </div>
    </aside>

    <!-- 2. MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navigation Navbar -->
        <header class="h-16 bg-white border-b border-[#E5E7EB] flex items-center justify-between px-8 z-10 shrink-0">
            <!-- Search bar placeholder -->
            <div class="relative w-72">
                <span class="absolute inset-y-0 left-3 flex items-center text-[#A1A1AA]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" placeholder="Cari..." class="w-full bg-[#F4F4F5] rounded-full py-1.5 pl-10 pr-4 text-sm focus:outline-none focus:ring-1 focus:ring-primary-500 text-[#18181B]">
            </div>

            <!-- Profile and Settings Actions -->
            <div class="flex items-center gap-4">
                <button class="w-9 h-9 rounded-full hover:bg-[#F4F4F5] flex items-center justify-center text-[#71717A]">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </button>
                <button class="w-9 h-9 rounded-full hover:bg-[#F4F4F5] flex items-center justify-center text-[#71717A]">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                </button>
                <button class="w-9 h-9 rounded-full hover:bg-[#F4F4F5] flex items-center justify-center text-[#71717A]">
                    <i data-lucide="help-circle" class="w-5 h-5"></i>
                </button>
                
                <div class="h-6 w-px bg-[#E5E7EB]"></div>

                <!-- Admin Profile Card -->
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-[#18181B] leading-none">Admin Name</p>
                        <p class="text-xs text-[#71717A]">Administrator</p>
                    </div>
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=80&q=80" alt="Avatar" class="w-9 h-9 rounded-full object-cover border border-[#E5E7EB]">
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 overflow-y-auto p-8 space-y-6">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-primary-900 tracking-tight">Dashboard Ringkasan</h2>
                    <p class="text-[#71717A] mt-1">Laporan grafis dan visualisasi program pelatihan.</p>
                </div>
                
                <!-- Statically Locked Year indicator -->
                <div class="bg-white border border-[#E5E7EB] rounded-full px-4 py-1.5 flex items-center gap-2 shadow-2xs">
                    <span class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></span>
                    <span class="text-sm font-bold text-[#18181B]">Tahun Aktif: 2026</span>
                </div>
            </div>

            <!-- Filter Controls for Dashboard -->
            <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 shadow-sm flex flex-col md:flex-row items-center gap-4">
                <div class="text-sm font-bold text-[#18181B] flex items-center gap-2 shrink-0">
                    <i data-lucide="filter" class="w-4 h-4 text-primary-500"></i> Filter Dashboard:
                </div>

                <!-- 1. Program Category Filter -->
                <div class="relative w-full md:w-64">
                    <select id="filterCategory" onchange="filterDashboard()" class="w-full appearance-none bg-white border border-[#E5E7EB] rounded-lg py-2 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-primary-100 focus:border-primary-500 text-[#18181B] font-medium">
                        <option value="">Semua Kategori Program</option>
                        <option value="Training Reguler - Staf">Training Reguler - Staf</option>
                        <option value="Training Reguler - Mandor">Training Reguler - Mandor</option>
                        <option value="Training Riau">Training Riau</option>
                        <option value="Training Kalbar">Training Kalbar</option>
                        <option value="Training Kaltim">Training Kaltim</option>
                        <option value="Training Corporate">Training Corporate</option>
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center text-[#71717A] pointer-events-none">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </span>
                </div>



                <!-- Reset Filter Link -->
                <button onclick="resetDashboardFilters()" class="text-xs font-semibold text-[#EF4444] hover:text-[#DC2626] ml-auto">
                    Reset Filter
                </button>
            </div>

            <!-- 4. DYNAMIC DASHBOARD METRICS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1: Total Programs -->
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 flex items-center gap-5 shadow-sm">
                    <div class="p-4 bg-emerald-50 text-emerald-600 rounded-full">
                        <i data-lucide="book-open" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#71717A]">Total Program</p>
                        <h4 id="statPrograms" class="text-2xl font-bold text-[#18181B] mt-1">0</h4>
                        <p class="text-xs text-[#71717A] mt-0.5 font-medium">Terdaftar</p>
                    </div>
                </div>

                <!-- Card 2: Total Batches -->
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 flex items-center gap-5 shadow-sm">
                    <div class="p-4 bg-blue-50 text-blue-600 rounded-full">
                        <i data-lucide="layers" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#71717A]">Total Rencana Batch</p>
                        <h4 id="statBatches" class="text-2xl font-bold text-[#18181B] mt-1">0</h4>
                        <p class="text-xs text-[#71717A] mt-0.5 font-medium">Rencana batch di 2026</p>
                    </div>
                </div>

                <!-- Card 3: Total Target Participants -->
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 flex items-center gap-5 shadow-sm">
                    <div class="p-4 bg-purple-50 text-purple-600 rounded-full">
                        <i data-lucide="users" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#71717A]">Total Target Peserta</p>
                        <h4 id="statParticipants" class="text-2xl font-bold text-[#18181B] mt-1">0 <span class="text-sm font-normal text-[#71717A]">org</span></h4>
                        <p class="text-xs text-[#71717A] mt-0.5 font-medium">Total kuota peserta</p>
                    </div>
                </div>
            </div>

            <!-- 5. CHART VISUALIZATION SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Chart A: Programs per Category -->
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 shadow-sm">
                    <h3 class="font-bold text-sm text-[#18181B] border-b border-[#E5E7EB] pb-3 mb-4 flex items-center gap-2">
                        <i data-lucide="pie-chart" class="w-4 h-4 text-primary-500"></i> Distribusi Program per Kategori
                    </h3>
                    <div class="h-64 flex justify-center items-center">
                        <canvas id="categoryDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Chart B: Batches per Category -->
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 shadow-sm">
                    <h3 class="font-bold text-sm text-[#18181B] border-b border-[#E5E7EB] pb-3 mb-4 flex items-center gap-2">
                        <i data-lucide="bar-chart-3" class="w-4 h-4 text-primary-500"></i> Rencana Jumlah Batch per Kategori
                    </h3>
                    <div class="h-64 flex justify-center items-center">
                        <canvas id="categoryBatchesChart"></canvas>
                    </div>
                </div>

                <!-- Chart C: Target Participants per Category -->
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 shadow-sm">
                    <h3 class="font-bold text-sm text-[#18181B] border-b border-[#E5E7EB] pb-3 mb-4 flex items-center gap-2">
                        <i data-lucide="bar-chart-2" class="w-4 h-4 text-primary-500"></i> Total Target Peserta per Kategori
                    </h3>
                    <div class="h-64 flex justify-center items-center">
                        <canvas id="categoryParticipantsChart"></canvas>
                    </div>
                </div>

                <!-- Chart D: Non-Regular Subsection Breakdown -->
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 shadow-sm">
                    <h3 class="font-bold text-sm text-[#18181B] border-b border-[#E5E7EB] pb-3 mb-4 flex items-center gap-2">
                        <i data-lucide="layout-grid" class="w-4 h-4 text-primary-500"></i> Distribusi Program Non-Reguler (Sub-Sektor)
                    </h3>
                    <div class="h-64 flex justify-center items-center">
                        <canvas id="nonRegBreakdownChart"></canvas>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <!-- Data passing from Controller to JavaScript & Filtering Logic -->
    <script>
        lucide.createIcons();

        // 112 Programs parsed array from controller
        const allPrograms = @json($programs);

        // Categories list
        const categories = [
            'Training Reguler - Staf',
            'Training Reguler - Mandor',
            'Training Riau',
            'Training Kalbar',
            'Training Kaltim',
            'Training Corporate'
        ];

        // Global instances of charts
        let chartDist, chartBatches, chartParticipants, chartNonReg;

        // Initialization function
        function initCharts() {
            // Chart A: Doughnut Category Distribution
            const ctxA = document.getElementById('categoryDistributionChart').getContext('2d');
            chartDist = new Chart(ctxA, {
                type: 'doughnut',
                data: {
                    labels: categories.map(c => c.replace('Training ', '')),
                    datasets: [{
                        data: [0, 0, 0, 0, 0, 0],
                        backgroundColor: ['#3b82f6', '#a855f7', '#4B6A34', '#87aa4e', '#eab308', '#f97316'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: { size: 11, weight: 'semibold' },
                                padding: 12
                            }
                        }
                    }
                }
            });

            // Chart B: Bar Chart Category Batches
            const ctxB = document.getElementById('categoryBatchesChart').getContext('2d');
            chartBatches = new Chart(ctxB, {
                type: 'bar',
                data: {
                    labels: categories.map(c => c.replace('Training ', '')),
                    datasets: [{
                        label: 'Jumlah Batch',
                        data: [0, 0, 0, 0, 0, 0],
                        backgroundColor: '#4B6A34',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#F1F1F1' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Chart C: Bar Chart Category Participants
            const ctxC = document.getElementById('categoryParticipantsChart').getContext('2d');
            chartParticipants = new Chart(ctxC, {
                type: 'bar',
                data: {
                    labels: categories.map(c => c.replace('Training ', '')),
                    datasets: [{
                        label: 'Target Peserta',
                        data: [0, 0, 0, 0, 0, 0],
                        backgroundColor: '#87aa4e',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#F1F1F1' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Chart D: Horizontal Bar Non-Reguler Subsections
            const ctxD = document.getElementById('nonRegBreakdownChart').getContext('2d');
            chartNonReg = new Chart(ctxD, {
                type: 'bar',
                data: {
                    labels: ['ESTATE', 'MILL', 'TRAKSI', 'DOWNSTREAM', 'ADMINISTRASI', 'SOFT SKILLS'],
                    datasets: [{
                        label: 'Jumlah Program',
                        data: [0, 0, 0, 0, 0, 0],
                        backgroundColor: ['#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899', '#3b82f6'],
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: '#F1F1F1' } },
                        y: { grid: { display: false } }
                    }
                }
            });
        }

        // Filtering and updates function
        function filterDashboard() {
            const selectedCat = document.getElementById('filterCategory').value;

            // 1. Filter the dataset
            const filtered = allPrograms.filter(p => {
                return selectedCat === '' || p.category === selectedCat;
            });

            // 2. Compute stats
            let totalPrograms = filtered.length;
            let totalBatches = 0;
            let totalParticipants = 0;

            filtered.forEach(p => {
                totalBatches += p.total_batches || 0;
                totalParticipants += p.target_participants.total || 0;
            });

            // Update KPI elements
            document.getElementById('statPrograms').innerText = totalPrograms.toLocaleString('id-ID');
            document.getElementById('statBatches').innerText = totalBatches.toLocaleString('id-ID');
            document.getElementById('statParticipants').innerHTML = totalParticipants.toLocaleString('id-ID') + ' <span class="text-sm font-normal text-[#71717A]">org</span>';

            // 3. Compute Chart A, B, C Data (by category)
            const catCounts = Array(6).fill(0);
            const catBatches = Array(6).fill(0);
            const catParticipants = Array(6).fill(0);

            filtered.forEach(p => {
                const catIndex = categories.indexOf(p.category);
                if (catIndex !== -1) {
                    catCounts[catIndex]++;
                    catBatches[catIndex] += p.total_batches || 0;
                    catParticipants[catIndex] += p.target_participants.total || 0;
                }
            });

            // 4. Compute Chart D Data (by subsection)
            const subCounts = Array(6).fill(0); // ESTATE, MILL, TRAKSI, DOWNSTREAM, ADMINISTRASI, SOFT SKILLS
            filtered.forEach(p => {
                if (p.sheet === 'Non-Reguler & Soft Skill') {
                    const sub = p.subsection || p.section;
                    if (sub && sub !== '') {
                        const cleanSub = sub.toUpperCase();
                        if (cleanSub.includes('ESTATE')) subCounts[0]++;
                        else if (cleanSub.includes('MILL')) subCounts[1]++;
                        else if (cleanSub.includes('TRAK')) subCounts[2]++;
                        else if (cleanSub.includes('DOWN')) subCounts[3]++;
                        else if (cleanSub.includes('ADMIN')) subCounts[4]++;
                        else if (cleanSub.includes('LEADER') || cleanSub.includes('SOFT')) subCounts[5]++;
                    }
                }
            });

            // 5. Update and refresh Charts
            chartDist.data.datasets[0].data = catCounts;
            chartDist.update();

            chartBatches.data.datasets[0].data = catBatches;
            chartBatches.update();

            chartParticipants.data.datasets[0].data = catParticipants;
            chartParticipants.update();

            chartNonReg.data.datasets[0].data = subCounts;
            chartNonReg.update();
        }

        // Reset all filters
        function resetDashboardFilters() {
            document.getElementById('filterCategory').value = '';
            filterDashboard();
        }

        // Initialize on load
        window.addEventListener('load', () => {
            initCharts();
            filterDashboard(); // compute initial full dataset stats
        });
    </script>
</body>
</html>
