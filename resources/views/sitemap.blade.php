{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    
    <!-- 1. Halaman Utama Portal -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->tz('Asia/Jakarta')->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc>{{ url('/tentang-kami') }}</loc>
        <lastmod>{{ now()->tz('Asia/Jakarta')->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- 2. Halaman Dinamis (Contoh: Daftar Pengumuman & Lowongan) -->
    @foreach($articles as $post)
    <url>
        <loc>{{ route('artikel.show', $post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->tz('Asia/Jakarta')->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

</urlset>
