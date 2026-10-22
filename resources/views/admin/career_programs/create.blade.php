<x-admin-layout>
    <x-slot name="title">Tambah Program Karir</x-slot>
    <x-slot name="header">Tambah Program Karir Baru</x-slot>
    <x-slot name="headerSub">Buat program karir baru yang akan ditampilkan di halaman publik.</x-slot>
    
    <x-slot name="headerAction">
        <a href="{{ route('admin.page_settings.index', ['tab' => 'karir']) }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition shadow-sm inline-flex items-center text-sm">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
    </x-slot>

    <!-- Trix Editor Assets -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    
    <!-- Restore Trix Editor List Styles overridden by Tailwind Preflight -->
    <style>
        trix-editor ul {
            list-style-type: disc !important;
            padding-left: 1.5rem !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }
        trix-editor ol {
            list-style-type: decimal !important;
            padding-left: 1.5rem !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }
        trix-editor li {
            margin-bottom: 0.25rem !important;
        }
        trix-toolbar [data-trix-button-group="file-tools"] {
            display: none;
        }
        trix-toolbar [data-trix-button] { background-color: white; }
        trix-toolbar [data-trix-button]:hover { background-color: #f9fafb; }
        trix-toolbar [data-trix-button].trix-active { background-color: #e5e7eb; }
        trix-editor { min-height: 250px; background-color: white; border-radius: 0.5rem; border-color: #e5e7eb !important; }
        trix-editor:focus { border-color: #509c38 !important; box-shadow: 0 0 0 1px #509c38 !important; outline: none; }
    </style>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden w-full">
        <form action="{{ route('admin.career_programs.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-6">
                <!-- Info Program -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-800 mb-2">Nama Program <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white focus:border-[#509c38] focus:ring focus:ring-green-200 transition-colors shadow-sm" required>
                    </div>
                    <div>
                        <label for="code" class="block text-sm font-bold text-gray-800 mb-2">Kode Program <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white focus:border-[#509c38] focus:ring focus:ring-green-200 transition-colors shadow-sm" required>
                    </div>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-bold text-gray-800 mb-2">Deskripsi Singkat <span class="text-red-500">*</span></label>
                    <textarea name="description" id="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white focus:border-[#509c38] focus:ring focus:ring-green-200 transition-colors shadow-sm" required>{{ old('description') }}</textarea>
                </div>

                <!-- Status Aktif -->
                <div>
                    <label class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-[#509c38] hover:bg-green-50/30 transition-all transition">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }} class="w-5 h-5 text-[#509c38] focus:ring-[#509c38] border-gray-300 rounded">
                        <div class="ml-3">
                            <span class="block text-sm font-semibold text-gray-900">Aktifkan Program Ini</span>
                            <span class="block text-xs text-gray-500">Jika dicentang, program ini akan muncul di halaman karir publik.</span>
                        </div>
                    </label>
                </div>

                <!-- Upload Foto Banner Card Depan -->
                <div>
                    <label for="image" class="block text-sm font-bold text-gray-800 mb-2">Foto / Visual Card Depan</label>
                    <div class="flex items-start gap-6 p-4 border border-gray-200 rounded-xl bg-gray-50/50">
                        <div class="shrink-0 w-44 h-28 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 flex items-center justify-center relative">
                            <img id="preview-img" src="{{ asset('images/bg_card_fat.jpg') }}" alt="Default Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow space-y-2">
                            <input type="file" name="image" id="image" accept="image/*"
                                   onchange="if(this.files && this.files[0]) { document.getElementById('preview-img').src = URL.createObjectURL(this.files[0]); }"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#509c38] file:text-white hover:file:bg-green-700 cursor-pointer">
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Format: JPG, PNG, WEBP (Maksimal 3MB). Rekomendasi rasio 16:9 atau foto landscape resolusi tinggi.
                                <span class="block text-gray-400 font-normal mt-1">Jika dikosongkan, sistem akan otomatis menggunakan visual lanskap default yang elegan.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Google Form URL -->
                <div>
                    <label for="gform_url" class="block text-sm font-bold text-gray-800 mb-2">Link Google Form (URL Pendaftaran)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="link" class="h-4 w-4 text-gray-400"></i>
                        </div>
                        <input type="url" name="gform_url" id="gform_url" value="{{ old('gform_url') }}"
                            class="pl-10 w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#509c38] focus:border-transparent transition"
                            placeholder="https://forms.gle/..." required>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Link ini akan digunakan untuk tombol pendaftaran dan generate QR Code.</p>
                </div>

                <!-- Qualifications (Trix) -->
                <div>
                    <label for="qualifications" class="block text-sm font-bold text-gray-800 mb-2">Kualifikasi Persyaratan</label>
                    <input id="x_qualifications" type="hidden" name="qualifications" value="{{ old('qualifications') }}">
                    <trix-editor input="x_qualifications" class="trix-content prose max-w-none text-gray-800 text-sm"></trix-editor>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-[#509c38] text-white font-bold rounded-xl hover:bg-green-700 transition shadow-md inline-flex items-center">
                    <i data-lucide="save" class="w-5 h-5 mr-2"></i> Simpan Program Baru
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener("trix-file-accept", function(event) {
            event.preventDefault(); // Prevent file uploads in qualifications
        });
    </script>
</x-admin-layout>
