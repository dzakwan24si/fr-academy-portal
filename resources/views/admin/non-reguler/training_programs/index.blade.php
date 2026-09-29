<x-admin-layout>
    <x-slot name="title">Program Training</x-slot>
    <x-slot name="header">Program Training</x-slot>
    <x-slot name="headerSub">Kelola data program training yang ada.</x-slot>

    <!-- Modal Component Wrapper -->
    <div x-data="crudModal('/training-reguler/training-programs')">
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6 mt-4">
            <div class="bg-emerald-600 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-emerald-600/30 cursor-pointer">
                <div class="relative z-10 text-white">
                    <p class="text-xs font-semibold mb-1 uppercase tracking-wider opacity-80">Total Program</p>
                    <p class="text-3xl font-black">{{ $total }}</p>
                </div>
                <div class="absolute -bottom-4 -right-4 opacity-10">
                    <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <!-- Decorative circle -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full opacity-5 -translate-y-1/2 translate-x-1/3"></div>
            </div>
            @if($totalFat > 0)
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-emerald-500 p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-slate-200/50 cursor-pointer">
                <div class="relative z-10">
                    <p class="text-[11px] text-slate-500 font-bold mb-1 uppercase tracking-widest">FAT</p>
                    <p class="text-2xl font-black text-slate-800">{{ $totalFat }} <span class="text-sm font-medium text-slate-400 lowercase">program</span></p>
                </div>
                <div class="absolute right-0 bottom-0 opacity-[0.03] transform translate-x-1/4 translate-y-1/4">
                    <svg class="w-24 h-24 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                </div>
            </div>
            @endif
            @if($totalTat > 0)
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-blue-500 p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-slate-200/50 cursor-pointer">
                <div class="relative z-10">
                    <p class="text-[11px] text-slate-500 font-bold mb-1 uppercase tracking-widest">TAT</p>
                    <p class="text-2xl font-black text-slate-800">{{ $totalTat }} <span class="text-sm font-medium text-slate-400 lowercase">program</span></p>
                </div>
                <div class="absolute right-0 bottom-0 opacity-[0.03] transform translate-x-1/4 translate-y-1/4">
                    <svg class="w-24 h-24 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
            </div>
            @endif
            @if($totalMat > 0)
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-amber-500 p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-slate-200/50 cursor-pointer">
                <div class="relative z-10">
                    <p class="text-[11px] text-slate-500 font-bold mb-1 uppercase tracking-widest">MAT</p>
                    <p class="text-2xl font-black text-slate-800">{{ $totalMat }} <span class="text-sm font-medium text-slate-400 lowercase">program</span></p>
                </div>
                <div class="absolute right-0 bottom-0 opacity-[0.03] transform translate-x-1/4 translate-y-1/4">
                    <svg class="w-24 h-24 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
            @endif
            @if($totalAat > 0)
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-purple-500 p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-slate-200/50 cursor-pointer">
                <div class="relative z-10">
                    <p class="text-[11px] text-slate-500 font-bold mb-1 uppercase tracking-widest">AAT</p>
                    <p class="text-2xl font-black text-slate-800">{{ $totalAat }} <span class="text-sm font-medium text-slate-400 lowercase">program</span></p>
                </div>
                <div class="absolute right-0 bottom-0 opacity-[0.03] transform translate-x-1/4 translate-y-1/4">
                    <svg class="w-24 h-24 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Daftar Program</h3>
                <button @click="openModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-all duration-200 ease-in-out hover:scale-105 active:scale-95 hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Program
                </button>
            </div>
            <div class="overflow-x-auto">
                <div class="w-full overflow-x-auto"><table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama Program</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Statistik</th>
                              <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($programs as $item)
                        <tr class="transition-colors duration-200 ease-in hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-800">{{ $item->name }}</span>
                            </td>
                            
                            <td class="px-6 py-4 text-sm text-slate-500">
                                <div class="truncate max-w-[150px] md:max-w-[200px]" title="{{ $item->description }}">
                                    {{ $item->description ?: '-' }}
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                  <div class="flex items-center gap-4">
                                      <div class="text-center">
                                          <p class="text-xs font-bold text-slate-700">{{ $item->daily_classes_count ?? 0 }}</p>
                                          <p class="text-[10px] uppercase text-slate-400 font-semibold tracking-wider">Materi</p>
                                      </div>
                                      <div class="w-px h-6 bg-slate-200"></div>
                                      <div class="text-center">
                                          <p class="text-xs font-bold text-[#28844b]">{{ $item->trainees_count ?? 0 }}</p>
                                          <p class="text-[10px] uppercase text-slate-400 font-semibold tracking-wider">Peserta</p>
                                      </div>
                                  </div>
                              </td>
                              
                              <td class="px-6 py-4 text-sm">
                                  <div class="flex items-center justify-end gap-2">
                                    <button @click="openModal({{ $item->toJson() }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:shadow-sm rounded-md transition-all duration-200 hover:-translate-y-0.5 font-semibold text-[11px] uppercase tracking-wide">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </button>
                                    <form action="{{ route('training-non-reguler.training-programs.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?');" class="inline-block m-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 hover:shadow-sm rounded-md transition-all duration-200 hover:-translate-y-0.5 font-semibold text-[11px] uppercase tracking-wide">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada data program.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
            @if($programs->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $programs->links() }}
            </div>
            @endif
        </div>

        <!-- Modal Overlay -->
        <x-admin-modal show="isOpen" maxWidth="3xl">
            <x-slot name="titleHtml"><span x-text="isEdit ? 'Edit Program Training' : 'Tambah Program Training Baru'"></span></x-slot>
            <x-slot name="subtitleHtml">Lengkapi formulir di bawah ini</x-slot>

                        <form :action="formAction" method="POST">
                            @csrf
                            <template x-if="isEdit">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div class="mb-4">
                                <h4 class="text-[#28844b] font-bold text-sm tracking-wide mb-4">1. DATA PROGRAM</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Program <span class="text-red-500">*</span></label>
                                        <select name="name" x-model="formData.name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-[#28844b] focus:border-[#28844b] outline-none bg-white">
                                            <option value="">-- Pilih Nama Program --</option>
                                            <option value="FAT I">FAT I</option>
                                            <option value="FAT II">FAT II</option>
                                            <option value="MAT I">MAT I</option>
                                            <option value="MAT II">MAT II</option>
                                            <option value="DMT I">DMT I</option>
                                            <option value="TAT I">TAT I</option>
                                            <option value="TAT II">TAT II</option>
                                            <option value="AAT I">AAT I</option>
                                            <option value="AAT II">AAT II</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4 mt-6">
                                <h4 class="text-[#28844b] font-bold text-sm tracking-wide mb-4">2. KETERANGAN</h4>
                                <div class="grid grid-cols-1 gap-5">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi</label>
                                        <textarea name="description" x-model="formData.description" rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-[#28844b] focus:border-[#28844b] outline-none"></textarea>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                                <button type="button" @click="isOpen = false" class="px-5 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition text-sm">Batal</button>
                                <button type="submit" class="px-5 py-2 bg-[#28844b] text-white font-bold rounded-lg hover:bg-green-700 transition flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                    Simpan Data
                                </button>
                            </div>
                        </form>
        </x-admin-modal>

    </div>

    <!-- AlpineJS CRUD Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('crudModal', (baseUrl) => ({
                isOpen: false,
                isEdit: false,
                baseUrl: baseUrl,
                formAction: '',
                formData: {
                    name: '',
                    type: '',
                    
                    description: '',
                    
                },
                openModal(data = null) {
                    this.isOpen = true;
                    if (data) {
                        this.isEdit = true;
                        this.formAction = this.baseUrl + '/' + data.id;
                        this.formData = { ...data };
                    } else {
                        this.isEdit = false;
                        this.formAction = this.baseUrl;
                        this.formData = {
                            name: '',
                            type: '',
                            
                            description: '',
                            
                        };
                    }
                }
            }));
        });
    </script>
</x-admin-layout>





