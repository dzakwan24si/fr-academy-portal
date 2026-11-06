<x-layout>
    <!-- Article Header -->
    <div class="relative bg-teal-primary min-h-80 flex flex-col justify-end pb-12 pt-32">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            @if($article->featured_image)
                <img src="{{ Storage::url($article->featured_image) }}" alt="Header Background" class="w-full h-full object-cover opacity-30" onerror="this.onerror=null; this.src='{{ asset('images/welcomefr.jpg') }}';">
            @else
                <div class="w-full h-full bg-[#114045] opacity-80"></div>
            @endif
        </div>
        
        <div class="relative z-10 max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <!-- Breadcrumbs -->
            <nav class="flex text-sm text-gray-300 mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="/" class="hover:text-white transition">BERANDA</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            <a href="/artikel" class="hover:text-white transition">ARTIKEL</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            <span class="text-white font-medium truncate max-w-[200px] md:max-w-md uppercase">{{ Str::limit($article->title, 40) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white leading-tight max-w-5xl">
                {{ $article->title }}
            </h1>
        </div>
    </div>

    <!-- Main Content Area (Diperlebar ke kanan dengan max-w-[1440px]) -->
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <!-- Left Sidebar (Lebar tetap ringkas 280-320px agar artikel bisa maksimal ke kanan) -->
            <div class="w-full lg:w-72 xl:w-80 shrink-0 space-y-10 order-2 lg:order-1">
                
                <!-- Kategori Lainnya -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-6 border-b dark:border-gray-700 pb-4">Kategori Lainnya</h3>
                    <ul class="space-y-4">
                        @foreach($categories as $cat)
                        <li><a href="/artikel?category={{ $cat->id }}" class="flex items-center text-gray-600 dark:text-gray-300 hover:text-green-accent dark:hover:text-green-400 transition">
                            <svg class="w-4 h-4 mr-3 text-green-accent dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            {{ $cat->name }}
                        </a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Berita Terbaru -->
                <div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-6">Berita Terbaru</h3>
                    <div class="space-y-6">
                        @forelse($latest_articles as $latest)
                        <!-- News Item -->
                        <a href="{{ route('artikel.show', $latest->slug) }}" class="block bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition cursor-pointer transition-colors duration-300">
                            @if($latest->featured_image)
                                <img src="{{ Storage::url($latest->featured_image) }}" alt="News" class="w-full h-32 object-cover">
                            @else
                                <div class="w-full h-32 bg-[#114045] flex items-center justify-center opacity-90">
                                    <img src="{{ asset('images/logofr.png') }}" class="w-20 opacity-30">
                                </div>
                            @endif
                            <div class="p-4">
                                <h4 class="font-bold text-teal-primary dark:text-white text-sm leading-tight mb-2 group-hover:text-green-accent transition">{{ $latest->title }}</h4>
                                <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $latest->published_at ? \Carbon\Carbon::parse($latest->published_at)->diffForHumans() : $latest->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                        @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada berita lainnya.</p>
                        @endforelse
                    </div>
                    
                    <a href="/artikel" class="inline-flex items-center text-teal-primary dark:text-teal-400 font-semibold hover:text-green-accent dark:hover:text-green-300 transition mt-6 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        Lihat Semua Berita
                    </a>
                </div>
            </div>

            <!-- Right Main Content (Mengisi sisa lebar penuh ke kanan) -->
            <div class="w-full lg:flex-1 min-w-0 order-1 lg:order-2">
                <article class="bg-white dark:bg-gray-800 p-8 md:p-12 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300 w-full">
                    <!-- Article Body -->
                    <div class="article-body prose prose-teal dark:prose-invert max-w-none w-full text-gray-700 dark:text-gray-300" style="max-width: 100%;">
                        {!! $article->rendered_content !!}
                    </div>

                    <!-- Article Meta (Penulis & Tanggal di bawah persis semula) -->
                    <div class="mt-12 pt-8 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center gap-6">
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="w-8 h-8 rounded-full bg-teal-primary text-white flex items-center justify-center mr-3 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            {{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->diffForHumans() : $article->created_at->diffForHumans() }}
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="w-8 h-8 rounded-full bg-teal-primary text-white flex items-center justify-center mr-3 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            {{ $article->author->name ?? 'Administrator' }}
                        </div>
                    </div>

                    <!-- Tags -->
                    @if(!empty($article->tags) && is_array($article->tags))
                    <div class="mt-8 flex flex-wrap gap-2">
                        @foreach($article->tags as $tag)
                        <a href="/artikel?tag={{ urlencode($tag) }}" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full text-xs font-semibold text-gray-600 dark:text-gray-300 hover:border-green-accent dark:hover:border-green-400 hover:text-green-accent dark:hover:text-green-400 transition">{{ $tag }}</a>
                        @endforeach
                    </div>
                    @endif
                </article>
            </div>
            
        </div>
    </div>

    <!-- Typography & List Styling for TinyMCE Content -->
    <style>
        .article-body {
            font-size: 1.05rem;
            line-height: 1.8;
            word-break: break-word;
        }
        .article-body p {
            margin-top: 1.15rem;
            margin-bottom: 1.15rem;
            line-height: 1.8;
        }
        .article-body h1 {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1.25;
            margin-top: 2.5rem;
            margin-bottom: 1.25rem;
            color: #114045;
        }
        .article-body h2 {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.3;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #114045;
        }
        .article-body h3 {
            font-size: 1.375rem;
            font-weight: 700;
            line-height: 1.4;
            margin-top: 1.75rem;
            margin-bottom: 0.75rem;
            color: #114045;
        }
        .article-body h4 {
            font-size: 1.15rem;
            font-weight: 600;
            line-height: 1.45;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            color: #114045;
        }
        .dark .article-body h1,
        .dark .article-body h2,
        .dark .article-body h3,
        .dark .article-body h4 {
            color: #f9fafb;
        }
        /* Bullet Lists */
        .article-body ul {
            list-style-type: disc !important;
            margin-top: 1.15rem !important;
            margin-bottom: 1.15rem !important;
            padding-left: 2rem !important;
        }
        .article-body ul ul {
            list-style-type: circle !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
            padding-left: 1.5rem !important;
        }
        .article-body ul ul ul {
            list-style-type: square !important;
        }
        /* Numbered Lists */
        .article-body ol {
            list-style-type: decimal !important;
            margin-top: 1.15rem !important;
            margin-bottom: 1.15rem !important;
            padding-left: 2rem !important;
        }
        .article-body ol ol {
            list-style-type: lower-alpha !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
            padding-left: 1.5rem !important;
        }
        .article-body ol ol ol {
            list-style-type: lower-roman !important;
        }
        /* List Items & Markers */
        .article-body li {
            display: list-item !important;
            margin-top: 0.45rem !important;
            margin-bottom: 0.45rem !important;
            line-height: 1.75 !important;
            padding-left: 0.25rem !important;
        }
        .article-body li::marker {
            color: #59A338;
            font-weight: 700;
        }
        .dark .article-body li::marker {
            color: #78c649;
        }
        /* Text Format */
        .article-body strong, .article-body b {
            font-weight: 700;
        }
        .article-body em, .article-body i {
            font-style: italic;
        }
        .article-body u {
            text-decoration: underline;
        }
        .article-body s, .article-body strike, .article-body del {
            text-decoration: line-through;
        }
        /* Links */
        .article-body a {
            color: #59A338;
            text-decoration: underline;
            font-weight: 500;
        }
        .article-body a:hover {
            color: #114045;
        }
        .dark .article-body a {
            color: #78c649;
        }
        /* Blockquote */
        .article-body blockquote {
            border-left: 4px solid #59A338;
            padding: 0.75rem 1.25rem;
            margin: 1.75rem 0;
            background-color: rgba(89, 163, 56, 0.05);
            border-radius: 0 0.75rem 0.75rem 0;
            font-style: italic;
            color: #4b5563;
        }
        .dark .article-body blockquote {
            background-color: rgba(255, 255, 255, 0.04);
            color: #d1d5db;
            border-left-color: #78c649;
        }
        /* Tables */
        .article-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.75rem 0;
            font-size: 0.95rem;
        }
        .article-body th, .article-body td {
            border: 1px solid #e5e7eb;
            padding: 0.75rem 1rem;
            text-align: left;
        }
        .dark .article-body th, .dark .article-body td {
            border-color: #374151;
        }
        .article-body th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #114045;
        }
        .dark .article-body th {
            background-color: #1f2937;
            color: #f9fafb;
        }
        /* Images */
        .article-body img {
            max-width: 100%;
            height: auto;
            border-radius: 0.75rem;
            margin: 1.75rem auto;
            display: block;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
        }
        /* Videos & Iframes (YouTube, etc) */
        .article-body iframe {
            max-width: 100%;
            aspect-ratio: 16 / 9;
            height: auto;
            border-radius: 0.75rem;
            margin: 1.75rem auto;
            display: block;
            border: 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
        }
        .article-body video {
            max-width: 100%;
            height: auto;
            border-radius: 0.75rem;
            margin: 1.75rem auto;
            display: block;
        }
    </style>
</x-layout>
