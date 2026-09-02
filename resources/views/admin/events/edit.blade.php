<x-admin-layout>
    <x-slot:title>
        Edit Tema Event
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            
            <div class="mb-8">
                <a href="{{ route('admin.events.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar Tema
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-bold text-gray-900">Edit Tema Event</h3>
                        <p class="mt-1 text-sm text-gray-500">Ubah informasi atau gambar dari tema event ini.</p>
                    </div>
                    @if($event->is_active)
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                            Sedang Aktif
                        </span>
                    @endif
                </div>
                
                <div class="p-6">
                    <form method="post" action="{{ route('admin.events.update', $event->id) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Nama Event -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Event / Tema</label>
                            <input type="text" name="name" id="name" required value="{{ old('name', $event->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#509c38] focus:border-[#509c38] sm:text-sm py-2 px-3 border" placeholder="Contoh: HUT Kemerdekaan RI ke-81">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gambar Event -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Overlay Saat Ini</label>
                            @if($event->image_path)
                                <div class="bg-gray-100 border border-gray-200 rounded-lg p-2 mb-4 h-40 flex items-center justify-center">
                                    <img src="{{ asset($event->image_path) }}" alt="{{ $event->name }}" class="max-h-full max-w-full object-contain">
                                </div>
                            @endif

                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Unggah Gambar Baru (Opsional)</label>
                            
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer" onclick="document.getElementById('image').click()">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-[#509c38] hover:text-green-600 focus-within:outline-none px-2 py-1">
                                            <span>Unggah file baru</span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/png, image/jpeg, image/gif" onchange="previewImage(event)">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">Kosongkan jika tidak ingin mengubah gambar.</p>
                                    <p class="text-xs font-semibold text-[#509c38] mt-2">💡 Tips: Gunakan ukuran 1920x1080 pixel (16:9) dengan area tengah transparan. Usahakan ukuran file di bawah 1 MB agar web tetap cepat.</p>
                                </div>
                            </div>
                            @error('image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Preview Container -->
                            <div id="imagePreviewContainer" class="mt-4 hidden">
                                <p class="text-sm font-medium text-gray-700 mb-2">Pratinjau Gambar Baru:</p>
                                <div class="bg-gray-200 border border-gray-300 rounded-lg h-48 flex items-center justify-center overflow-hidden">
                                    <img id="imagePreview" src="#" alt="Pratinjau" class="max-h-full max-w-full object-contain">
                                </div>
                            </div>
                        </div>

                        <!-- Status Aktif -->
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_active" name="is_active" type="checkbox" value="1" {{ $event->is_active ? 'checked' : '' }} class="focus:ring-[#509c38] h-4 w-4 text-[#509c38] border-gray-300 rounded cursor-pointer">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_active" class="font-bold text-gray-800 cursor-pointer">Aktifkan Tema Ini?</label>
                                    <p class="text-gray-500 mt-1">Jika dicentang, tema ini akan langsung ditampilkan di website dan menggantikan tema lain yang sedang aktif.</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                            <a href="{{ route('admin.events.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#509c38]">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#509c38] hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#509c38]">
                                Perbarui Tema
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Preview Image -->
    <script>
        function previewImage(event) {
            const input = event.target;
            const container = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                container.classList.add('hidden');
                preview.src = '#';
            }
        }
    </script>
</x-admin-layout>
