<x-admin-layout>
    <x-slot name="title">{{ isset($article) ? 'Edit Artikel' : 'Tulis Artikel' }}</x-slot>
    <x-slot name="header">{{ isset($article) ? 'Edit Artikel' : 'Tulis Artikel Baru' }}</x-slot>
    <x-slot name="headerSub">Editor konten informasi & edukasi FR Academy.</x-slot>
    
    <x-slot name="headerAction">
        <a href="{{ route('admin.artikel.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition shadow-sm inline-flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </x-slot>

    <!-- TinyMCE Rich Text Editor -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ isset($article) ? route('admin.artikel.update', $article) : route('admin.artikel.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @if(isset($article))
                @method('PUT')
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terdapat {{ $errors->count() }} kesalahan pada form:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column (Main Content) -->
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Judul Artikel</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $article->title ?? '') }}" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#509c38] focus:border-transparent transition"
                            placeholder="Masukkan judul yang menarik...">
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">Konten Artikel</label>
                        <textarea id="content" name="content" class="w-full">{{ old('content', isset($article) ? preg_replace('/(?<=src=["\'])\/storage\//', url('/storage') . '/', $article->content) : '') }}</textarea>
                    </div>
                </div>

                <!-- Right Column (Sidebar Settings) -->
                <div class="space-y-6">
                    <!-- Thumbnail Setup -->
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <h4 class="text-sm font-bold text-gray-700 mb-3 border-b border-gray-200 pb-2">Featured Image</h4>
                        
                        @if(isset($article) && $article->featured_image)
                            <div class="mb-3">
                                <img src="{{ Storage::url($article->featured_image) }}" alt="Current Image" class="w-full h-32 object-cover rounded-lg border border-gray-200">
                                <p class="text-xs text-gray-500 mt-1">Gambar saat ini</p>
                            </div>
                        @endif

                        <label class="block">
                            <span class="sr-only">Pilih Gambar</span>
                            <input type="file" name="featured_image" accept="image/*"
                                class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-green-50 file:text-green-700
                                hover:file:bg-green-100 transition cursor-pointer"
                            />
                        </label>
                        <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG. Maksimal 2MB.</p>
                    </div>

                    <!-- Category Setup -->
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <h4 class="text-sm font-bold text-gray-700 mb-3 border-b border-gray-200 pb-2">Kategori</h4>
                        <select name="category_id" class="w-full bg-white border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#509c38] text-gray-700 cursor-pointer">
                            <option value="">-- Tanpa Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $article->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tags Setup -->
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <h4 class="text-sm font-bold text-gray-700 mb-3 border-b border-gray-200 pb-2">Tag (Opsional)</h4>
                        <input type="text" name="tags" value="{{ old('tags', isset($article) && is_array($article->tags) ? implode(', ', $article->tags) : '') }}"
                            class="w-full bg-white border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#509c38] transition text-gray-700"
                            placeholder="Contoh: Lowongan, Riset, 2026">
                        <p class="text-xs text-gray-400 mt-2">Pisahkan dengan koma.</p>
                    </div>

                    <!-- Status Setup -->
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <h4 class="text-sm font-bold text-gray-700 mb-3 border-b border-gray-200 pb-2">Status Publikasi</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="draft" {{ old('status', $article->status ?? 'draft') === 'draft' ? 'checked' : '' }} class="h-4 w-4 text-[#509c38] focus:ring-[#509c38] border-gray-300">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Draft (Simpan Sementara)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="published" {{ old('status', $article->status ?? '') === 'published' ? 'checked' : '' }} class="h-4 w-4 text-[#509c38] focus:ring-[#509c38] border-gray-300">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Publish (Langsung Tampil)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-[#509c38] text-white font-bold rounded-xl hover:bg-green-700 transition shadow-md inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    {{ isset($article) ? 'Simpan Perubahan' : 'Simpan & Publikasikan' }}
                </button>
            </div>
        </form>
    </div>

    <!-- TinyMCE Initialization & Automatic Image Upload -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            tinymce.init({
                selector: '#content',
                height: 520,
                menubar: 'file edit view insert format tools table',
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks fontfamily fontsize | ' +
                    'bold italic underline strikethrough forecolor backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | link image media table | fullscreen code',
                image_title: true,
                automatic_uploads: true,
                image_caption: true,
                image_advtab: true,
                image_dimensions: true,
                paste_data_images: true,
                relative_urls: false,
                remove_script_host: false,
                convert_urls: false,
                media_live_embeds: true,
                media_url_resolver: function (data, resolve) {
                    if (data.url.indexOf('youtube') !== -1 || data.url.indexOf('youtu.be') !== -1) {
                        let videoId = '';
                        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                        const match = data.url.match(regExp);
                        if (match && match[2].length === 11) {
                            videoId = match[2];
                        }
                        if (videoId) {
                            const embedHtml = '<div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 12px; margin: 20px 0;"><iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" src="https://www.youtube.com/embed/' + videoId + '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>';
                            resolve({ html: embedHtml });
                            return;
                        }
                    }
                    if (data.url.indexOf('vimeo.com') !== -1) {
                        const regExp = /vimeo.*\/(\d+)/i;
                        const match = data.url.match(regExp);
                        if (match && match[1]) {
                            const embedHtml = '<div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 12px; margin: 20px 0;"><iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" src="https://player.vimeo.com/video/' + match[1] + '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe></div>';
                            resolve({ html: embedHtml });
                            return;
                        }
                    }
                    resolve({ html: '' });
                },
                // Handle image upload via AJAX to server storage
                images_upload_handler: function (blobInfo, progress) {
                    return new Promise(function (resolve, reject) {
                        const xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', '{{ route('admin.artikel.upload-image') }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                        xhr.upload.onprogress = function (e) {
                            progress(e.loaded / e.total * 100);
                        };

                        xhr.onload = function () {
                            if (xhr.status === 403 || xhr.status === 419) {
                                reject({ message: 'HTTP Error: ' + xhr.status + ' (Sesi berakhir, silakan login ulang)', remove: true });
                                return;
                            }
                            if (xhr.status < 200 || xhr.status >= 300) {
                                reject('HTTP Error: ' + xhr.status);
                                return;
                            }

                            try {
                                const json = JSON.parse(xhr.responseText);
                                if (!json || typeof json.location !== 'string') {
                                    reject('Invalid JSON: ' + xhr.responseText);
                                    return;
                                }
                                resolve(json.location);
                            } catch (err) {
                                reject('Gagal memproses respon server: ' + err.message);
                            }
                        };

                        xhr.onerror = function () {
                            reject('Gagal mengunggah gambar. Periksa koneksi jaringan.');
                        };

                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());

                        xhr.send(formData);
                    });
                },
                content_style: 'body { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 15px; color: #1f2937; line-height: 1.7; padding: 12px; } img { max-width: 100%; height: auto; }',
                promotion: false,
                branding: false,
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
        });
    </script>
</x-admin-layout>
