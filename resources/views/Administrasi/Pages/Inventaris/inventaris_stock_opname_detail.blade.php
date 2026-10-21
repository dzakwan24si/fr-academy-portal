<x-admin-layout>

<x-slot name="title">Detail Stock Opname | FR Academy</x-slot>
    <x-slot name="header">Detail Stock Opname | FR Academy</x-slot>



{{-- ============================== BREADCRUMB ============================== --}}
<nav class="flex items-center gap-2 text-xs text-[#71717A] mb-3">
    <span>Inventaris</span>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <a href="{{ route('administrasi.inventaris.stock-opname', !empty($jenisLokasiFilter) ? ['jenis_lokasi' => $jenisLokasiFilter] : []) }}" class="hover:text-[#4B6A34] transition-colors">Stock Opname</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span class="text-[#18181B] font-semibold">Detail</span>
</nav>

{{-- ============================== HEADER & SUB-TABS ============================== --}}
<div class="flex flex-col gap-4 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Stock Opname: {{ $formattedDate }}</h1>
                @if(strtolower($sessionMeta->status ?? 'selesai') === 'draf')
                    <span class="px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase bg-[#EFF6FF] text-[#2563EB] border border-[#BFDBFE]">
                        DRAF
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase bg-[#EAF5E9] text-[#2F6B27] border border-[#C6E6C1]">
                        SELESAI
                    </span>
                @endif
            </div>
            <p class="text-sm text-[#71717A] mt-1">
                {{ ($sessionMeta->catatan_sesi ?? null) ?: 'Stock Opname rutin inventaris' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            {{-- Location Switcher Pills --}}
            <div class="inline-flex bg-[#F4F4F5] border border-[#E5E7EB] rounded-xl p-1 shadow-sm">
                <a href="{{ route('administrasi.inventaris.stock-opname.detail', array_merge(['date' => $date], request()->query(), ['jenis_lokasi' => ''])) }}"
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ empty($jenisLokasiFilter) ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-white' }}">
                    Semua
                </a>
                <a href="{{ route('administrasi.inventaris.stock-opname.detail', array_merge(['date' => $date], request()->query(), ['jenis_lokasi' => 'Gudang'])) }}"
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $jenisLokasiFilter === 'Gudang' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-white' }}">
                    Gudang
                </a>
                <a href="{{ route('administrasi.inventaris.stock-opname.detail', array_merge(['date' => $date], request()->query(), ['jenis_lokasi' => 'Kantor'])) }}"
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $jenisLokasiFilter === 'Kantor' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-white' }}">
                    Kantor
                </a>
            </div>

            {{-- Feature Sub-tabs --}}
            <div class="inline-flex bg-white border border-[#E5E7EB] rounded-xl p-1 shadow-sm overflow-x-auto max-w-full">
                <a href="{{ route('administrasi.inventaris.barang') }}"
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all whitespace-nowrap {{ $activeTab === 'barang' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                    Barang
                </a>
                <a href="{{ route('administrasi.inventaris.pembelian') }}"
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all whitespace-nowrap {{ $activeTab === 'pembelian' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                    Pembelian
                </a>
                <a href="{{ route('administrasi.inventaris.stock-opname') }}"
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all whitespace-nowrap {{ $activeTab === 'stock_opname' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                    Stock Opname
                </a>
            </div>
        </div>
    </div>

    {{-- Action Buttons Row --}}
    <div class="flex flex-wrap items-center justify-start sm:justify-end gap-2.5">
        {{-- Edit Sesi & Status --}}
        <button onclick="document.getElementById('modalEditSession').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs font-semibold rounded-xl hover:bg-[#F9FAFB] transition-all shadow-sm">
            <i data-lucide="settings-2" class="w-3.5 h-3.5 text-[#4B6A34]"></i>
            <span>Edit Status & Sesi</span>
        </button>

        {{-- Hapus Sesi --}}
        <form action="{{ route('administrasi.inventaris.stock-opname.session.destroy', $date) }}" method="POST"
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh sesi Stock Opname tanggal {{ $formattedDate }} ini?');" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-200 text-red-600 text-xs font-semibold rounded-xl hover:bg-red-50 transition-all shadow-sm">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                <span>Hapus Sesi</span>
            </button>
        </form>

        {{-- Tambah Barang ke Stock Opname --}}
        <button onclick="document.getElementById('modalAddItem').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#4B6A34] text-white text-xs font-semibold rounded-xl hover:bg-[#3d562a] transition-all shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Barang</span>
        </button>
    </div>
</div>

{{-- ============================== FLASH ALERTS ============================== --}}
@if(session('success'))
<div class="mb-5 flex items-center gap-2 bg-[#F0F5EB] border border-[#CBDDA8] text-[#2F4321] text-sm px-4 py-3 rounded-xl shadow-sm" id="flash-msg">
    <i data-lucide="check-circle-2" class="w-4 h-4 text-[#4B6A34] shrink-0"></i>
    <span class="font-medium">{{ session('success') }}</span>
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-auto text-[#71717A] hover:text-[#18181B]">
        <i data-lucide="x" class="w-4 h-4"></i>
    </button>
</div>
@endif

@if($errors->any())
<div class="mb-5 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl shadow-sm">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ============================== SEARCH & ROOM FILTER TOOLBAR ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('administrasi.inventaris.stock-opname.detail', $date) }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full sm:w-auto flex-1">
            {{-- Search Input --}}
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-[#A1A1AA]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama asset..."
                       class="w-full pl-10 pr-4 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] placeholder-[#A1A1AA] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] transition-all">
            </div>

            {{-- Filter Lokasi Spesifik / Ruangan --}}
            <select name="lokasi_spesifik" onchange="this.form.submit()" class="px-3.5 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] font-medium focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                <option value="">Semua Ruangan</option>
                @foreach($availableRooms ?? [] as $room)
                    <option value="{{ $room }}" {{ ($lokasiSpesifikFilter ?? '') === $room ? 'selected' : '' }}>{{ $room }}</option>
                @endforeach
            </select>
        </div>

        {{-- Export CSV --}}
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <a href="{{ route('administrasi.inventaris.stock-opname.export', $date) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-[#E5E7EB] text-[#52525B] hover:text-[#18181B] hover:bg-[#F9FAFB] text-xs font-semibold rounded-xl transition-colors shadow-sm" title="Export Excel">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </form>
</div>

{{-- ============================== DETAILED TABLE ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-xs font-bold uppercase tracking-wider">
                    <th class="px-5 py-4 w-14 text-center">No</th>
                    <th class="px-5 py-4">Nama Asset</th>
                    <th class="px-4 py-4 text-center">Satuan</th>
                    <th class="px-4 py-4 text-center">Jenis Lokasi</th>
                    <th class="px-5 py-4">Lokasi Spesifik / Ruangan</th>
                    <th class="px-4 py-4 text-center text-emerald-700">Baik</th>
                    <th class="px-4 py-4 text-center text-rose-600">Rusak</th>
                    <th class="px-5 py-4 text-center bg-[#EEF2F6] text-[#1E293B]">Grand Total</th>
                    <th class="px-5 py-4">Keterangan</th>
                    <th class="px-4 py-4 text-center w-20">Foto</th>
                    <th class="px-4 py-4 text-center w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB] text-[#18181B]">
                @forelse($items as $index => $item)
                <tr class="hover:bg-[#F9FAFB] transition-colors">
                    <td class="px-5 py-4 text-center text-[#71717A] font-medium">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-5 py-4 font-bold text-[#18181B]">
                        {{ $item->barang->nama_asset ?? '-' }}
                    </td>
                    <td class="px-4 py-4 text-center text-[#71717A]">
                        {{ $item->barang->satuan ?? 'Unit' }}
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if(($item->barang->jenis_lokasi ?? 'Kantor') === 'Kantor')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Kantor
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Gudang
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 font-semibold text-[#374151]">
                        @if($item->lokasi_spesifik)
                            <span class="inline-flex items-center gap-1 bg-[#F9FAFB] px-2.5 py-1 rounded-lg border border-[#E5E7EB] text-xs">
                                <i data-lucide="map-pin" class="w-3 h-3 text-amber-600"></i>
                                {{ $item->lokasi_spesifik }}
                            </span>
                        @else
                            <span class="text-[#A1A1AA] text-xs italic">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center font-bold text-emerald-700">
                        {{ number_format($item->jumlah_baik) }}
                    </td>
                    <td class="px-4 py-4 text-center font-bold {{ $item->jumlah_rusak > 0 ? 'text-rose-600' : 'text-[#71717A]' }}">
                        {{ number_format($item->jumlah_rusak) }}
                    </td>
                    <td class="px-5 py-4 text-center font-extrabold text-[#1E293B] bg-[#F0F4F8]">
                        {{ number_format($item->grand_total) }}
                    </td>
                    <td class="px-5 py-4 text-xs text-[#52525B] max-w-xs leading-relaxed">
                        {{ $item->keterangan ?: '-' }}
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($item->foto_kerusakan)
                            <button onclick="openPhotoModal('{{ asset('storage/' . $item->foto_kerusakan) }}', '{{ $item->barang->nama_asset ?? 'Asset' }}')"
                                     class="p-2 text-[#4B6A34] hover:bg-[#F0F5EB] rounded-lg transition-colors"
                                     title="Lihat Foto Kerusakan">
                                <i data-lucide="image" class="w-4 h-4 text-[#4B6A34]"></i>
                            </button>
                        @else
                            <span class="text-[#D1D5DB]" title="Tidak ada foto">
                                <i data-lucide="image-off" class="w-4 h-4 inline"></i>
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openEditItemModal({{ json_encode($item) }})"
                                     class="p-1.5 text-[#52525B] hover:text-[#4B6A34] hover:bg-[#F4F4F5] rounded-lg transition-colors"
                                     title="Edit Kondisi">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('administrasi.inventaris.stock-opname.item.destroy', array_merge(['id' => $item->id], !empty($jenisLokasiFilter) ? ['jenis_lokasi' => $jenisLokasiFilter] : [])) }}" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pemeriksaan item ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                @if(!empty($jenisLokasiFilter))
                                    <input type="hidden" name="jenis_lokasi" value="{{ $jenisLokasiFilter }}">
                                @endif
                                <button type="submit" class="p-1.5 text-[#71717A] hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus Item">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-6 py-12 text-center text-[#A1A1AA]">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i data-lucide="clipboard-x" class="w-8 h-8 text-[#A1A1AA]"></i>
                            <p class="font-medium text-sm">Belum ada rincian data aset yang dimasukkan pada sesi Stock Opname ini.</p>
                            <p class="text-xs text-[#71717A]">Klik tombol "+ Tambah Barang" untuk mulai mencatat kondisi fisik aset.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Datalist for Room Suggestions --}}
<datalist id="roomSuggestions">
    <option value="Planter">
    <option value="Meeting">
    <option value="Administrasi">
    <option value="Loyality & Integrity">
    <option value="Diligance">
    <option value="Persistance & Care">
    <option value="Pantry & Solat">
    <option value="Academy Director">
    <option value="Office Staf">
    <option value="E-Learning">
    <option value="Assasmenet Mill">
    <option value="Assasmenet Estate">
    <option value="Mentoring">
    <option value="Loby">
    <option value="Server">
</datalist>

{{-- ============================== MODAL: TAMBAH BARANG KE STOCK OPNAME ============================== --}}
<div id="modalAddItem" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl border border-[#E5E7EB] mx-4 max-h-[90vh] overflow-y-auto animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-[#E5E7EB]">
            <h3 class="text-lg font-bold text-[#18181B]">Tambah Kondisi Barang</h3>
            <button onclick="document.getElementById('modalAddItem').classList.add('hidden')" class="text-[#A1A1AA] hover:text-[#18181B]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('administrasi.inventaris.stock-opname.item.store', $date) }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4">
            @csrf
            @if(!empty($jenisLokasiFilter))
                <input type="hidden" name="jenis_lokasi" value="{{ $jenisLokasiFilter }}">
            @endif

            @if($jenisLokasiFilter === 'Gudang')
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 shrink-0 text-emerald-600"></i>
                    <span>Mode Filter <strong>Gudang</strong>: Menampilkan aset Gudang saja. Lokasi spesifik tidak diperlukan.</span>
                </div>
            @elseif($jenisLokasiFilter === 'Kantor')
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 shrink-0 text-amber-600"></i>
                    <span>Mode Filter <strong>Kantor</strong>: Menampilkan aset Kantor saja. Lokasi spesifik (ruangan) wajib diisi.</span>
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Pilih Aset Barang <span class="text-red-500">*</span></label>
                <select id="add_item_barang_id" name="barang_id" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($allBarang as $b)
                        <option value="{{ $b->id }}" data-lokasi="{{ $b->jenis_lokasi ?? $b->lokasi }}">{{ $b->nama_asset }} ({{ $b->satuan }}) - {{ $b->jenis_lokasi }}</option>
                    @endforeach
                </select>
            </div>

            <div id="container_add_item_lokasi_spesifik" class="{{ $jenisLokasiFilter === 'Gudang' ? 'hidden' : '' }}">
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Lokasi Spesifik / Ruangan Kantor <span id="add_item_lokasi_spesifik_req" class="text-red-500 {{ $jenisLokasiFilter === 'Kantor' ? '' : 'hidden' }}">*</span></label>
                <input type="text" id="add_item_lokasi_spesifik" name="lokasi_spesifik" list="roomSuggestions" placeholder="Contoh: Planter, Meeting, Administrasi..." {{ $jenisLokasiFilter === 'Kantor' ? 'required' : '' }}
                       class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                <p id="add_item_lokasi_spesifik_hint" class="text-[11px] text-[#71717A] mt-1">{{ $jenisLokasiFilter === 'Kantor' ? 'Wajib mengisi ruangan untuk barang Kantor.' : 'Kosongkan untuk barang Gudang atau jika tidak merinci ruangan.' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3 bg-[#F9FAFB] p-3.5 rounded-xl border border-[#E5E7EB]">
                <div>
                    <label class="block text-xs font-bold text-emerald-700 mb-1">Jumlah Baik <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_baik" min="0" value="0" required
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-xl text-sm text-center font-bold text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-rose-600 mb-1">Jumlah Rusak <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_rusak" min="0" value="0" required
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-xl text-sm text-center font-bold text-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Keterangan / Temuan Lapangan</label>
                <textarea name="keterangan" rows="2" placeholder="Catatan kerusakan atau penempatan..."
                          class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Foto Kerusakan (Opsional)</label>
                <input type="file" name="foto_kerusakan" accept="image/*"
                       class="w-full text-xs text-[#71717A] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#F0F5EB] file:text-[#4B6A34] hover:file:bg-[#CBDDA8]">
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalAddItem').classList.add('hidden')"
                        class="px-4 py-2 border border-[#E5E7EB] text-[#52525B] text-sm font-semibold rounded-xl hover:bg-[#F4F4F5] transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#4B6A34] text-white text-sm font-semibold rounded-xl hover:bg-[#3d562a] transition-colors shadow-sm">
                    Simpan Item
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== MODAL: EDIT BARANG STOCK OPNAME ============================== --}}
<div id="modalEditItem" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl border border-[#E5E7EB] mx-4 max-h-[90vh] overflow-y-auto animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-[#E5E7EB]">
            <h3 class="text-lg font-bold text-[#18181B]">Edit Kondisi Barang</h3>
            <button onclick="document.getElementById('modalEditItem').classList.add('hidden')" class="text-[#A1A1AA] hover:text-[#18181B]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEditItem" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4">
            @csrf
            @method('PUT')
            @if(!empty($jenisLokasiFilter))
                <input type="hidden" name="jenis_lokasi" value="{{ $jenisLokasiFilter }}">
            @endif

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Aset Barang <span class="text-red-500">*</span></label>
                <select id="edit_item_barang_id" name="barang_id" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    @foreach($allBarang as $b)
                        <option value="{{ $b->id }}" data-lokasi="{{ $b->jenis_lokasi ?? $b->lokasi }}">{{ $b->nama_asset }} ({{ $b->satuan }}) - {{ $b->jenis_lokasi }}</option>
                    @endforeach
                </select>
            </div>

            <div id="container_edit_item_lokasi_spesifik" class="{{ $jenisLokasiFilter === 'Gudang' ? 'hidden' : '' }}">
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Lokasi Spesifik / Ruangan Kantor <span id="edit_item_lokasi_spesifik_req" class="text-red-500 {{ $jenisLokasiFilter === 'Kantor' ? '' : 'hidden' }}">*</span></label>
                <input type="text" id="edit_item_lokasi_spesifik" name="lokasi_spesifik" list="roomSuggestions" placeholder="Contoh: Planter, Meeting, Administrasi..." {{ $jenisLokasiFilter === 'Kantor' ? 'required' : '' }}
                       class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                <p id="edit_item_lokasi_spesifik_hint" class="text-[11px] text-[#71717A] mt-1">{{ $jenisLokasiFilter === 'Kantor' ? 'Wajib mengisi ruangan untuk barang Kantor.' : 'Kosongkan untuk barang Gudang atau jika tidak merinci ruangan.' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3 bg-[#F9FAFB] p-3.5 rounded-xl border border-[#E5E7EB]">
                <div>
                    <label class="block text-xs font-bold text-emerald-700 mb-1">Jumlah Baik</label>
                    <input type="number" id="edit_item_baik" name="jumlah_baik" min="0"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-xl text-sm text-center font-bold text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-rose-600 mb-1">Jumlah Rusak</label>
                    <input type="number" id="edit_item_rusak" name="jumlah_rusak" min="0"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-xl text-sm text-center font-bold text-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Keterangan / Temuan Lapangan</label>
                <textarea id="edit_item_keterangan" name="keterangan" rows="2"
                          class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Ganti Foto Kerusakan (Opsional)</label>
                <input type="file" name="foto_kerusakan" accept="image/*"
                       class="w-full text-xs text-[#71717A] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#F0F5EB] file:text-[#4B6A34] hover:file:bg-[#CBDDA8]">
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalEditItem').classList.add('hidden')"
                        class="px-4 py-2 border border-[#E5E7EB] text-[#52525B] text-sm font-semibold rounded-xl hover:bg-[#F4F4F5] transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#4B6A34] text-white text-sm font-semibold rounded-xl hover:bg-[#3d562a] transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== MODAL: EDIT SESI STOCK OPNAME ============================== --}}
<div id="modalEditSession" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[#E5E7EB] mx-4 animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-[#E5E7EB]">
            <h3 class="text-lg font-bold text-[#18181B]">Edit Sesi Stock Opname</h3>
            <button onclick="document.getElementById('modalEditSession').classList.add('hidden')" class="text-[#A1A1AA] hover:text-[#18181B]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('administrasi.inventaris.stock-opname.session.update', $date) }}" method="POST" class="mt-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Tanggal Audit</label>
                <input type="text" value="{{ $formattedDate }}" disabled
                       class="w-full px-3.5 py-2.5 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm text-[#71717A] cursor-not-allowed">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Status Sesi <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    <option value="Selesai" {{ ($sessionMeta->status ?? '') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Draf" {{ ($sessionMeta->status ?? '') === 'Draf' ? 'selected' : '' }}>Draf</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Catatan / Ringkasan Sesi</label>
                <textarea name="catatan_sesi" rows="3"
                          class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">{{ $sessionMeta->catatan_sesi ?? '' }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalEditSession').classList.add('hidden')"
                        class="px-4 py-2 border border-[#E5E7EB] text-[#52525B] text-sm font-semibold rounded-xl hover:bg-[#F4F4F5] transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#4B6A34] text-white text-sm font-semibold rounded-xl hover:bg-[#3d562a] transition-colors shadow-sm">
                    Simpan Sesi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== MODAL: PREVIEW FOTO KERUSAKAN ============================== --}}
<div id="modalPhotoPreview" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md hidden" onclick="closePhotoModal(event)">
    <div class="bg-white rounded-2xl p-4 w-full max-w-2xl shadow-2xl border border-[#E5E7EB] mx-4 animate-in zoom-in-95 duration-200" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 border-b border-[#E5E7EB] mb-3">
            <h3 class="text-sm font-bold text-[#18181B]" id="photoPreviewTitle">Foto Kerusakan Aset</h3>
            <button onclick="document.getElementById('modalPhotoPreview').classList.add('hidden')" class="text-[#A1A1AA] hover:text-[#18181B]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="flex items-center justify-center bg-[#F9FAFB] rounded-xl overflow-hidden max-h-[70vh]">
            <img id="photoPreviewImg" src="" alt="Foto Kerusakan" class="max-w-full max-h-[65vh] object-contain rounded-lg">
        </div>
    </div>
</div>



@push('scripts')
<script>
    const currentJenisLokasiFilter = @json($jenisLokasiFilter ?? '');

    function updateLokasiSpesifikField(selectElem, containerId, inputId, reqId, hintId) {
        const container = document.getElementById(containerId);
        const input = document.getElementById(inputId);
        const req = document.getElementById(reqId);
        const hint = document.getElementById(hintId);

        if (!container || !input) return;

        let lokasi = currentJenisLokasiFilter;
        if (!lokasi && selectElem) {
            const opt = selectElem.options[selectElem.selectedIndex];
            lokasi = opt ? (opt.getAttribute('data-lokasi') || '') : '';
        }

        if (lokasi.toLowerCase() === 'gudang') {
            container.classList.add('hidden');
            input.required = false;
            input.value = '';
            if (req) req.classList.add('hidden');
        } else if (lokasi.toLowerCase() === 'kantor') {
            container.classList.remove('hidden');
            input.required = true;
            if (req) req.classList.remove('hidden');
            if (hint) hint.innerText = 'Wajib mengisi ruangan untuk barang Kantor.';
        } else {
            container.classList.remove('hidden');
            input.required = false;
            if (req) req.classList.add('hidden');
            if (hint) hint.innerText = 'Kosongkan untuk barang Gudang atau jika tidak merinci ruangan.';
        }
    }

    const addItemBarangSelect = document.getElementById('add_item_barang_id');
    if (addItemBarangSelect) {
        addItemBarangSelect.addEventListener('change', function() {
            updateLokasiSpesifikField(this, 'container_add_item_lokasi_spesifik', 'add_item_lokasi_spesifik', 'add_item_lokasi_spesifik_req', 'add_item_lokasi_spesifik_hint');
        });
    }

    const editItemBarangSelect = document.getElementById('edit_item_barang_id');
    if (editItemBarangSelect) {
        editItemBarangSelect.addEventListener('change', function() {
            updateLokasiSpesifikField(this, 'container_edit_item_lokasi_spesifik', 'edit_item_lokasi_spesifik', 'edit_item_lokasi_spesifik_req', 'edit_item_lokasi_spesifik_hint');
        });
    }

    function openEditItemModal(item) {
        const form = document.getElementById('formEditItem');
        form.action = `/administrasi/inventaris/stock-opname/item/${item.id}`;
        const editSelect = document.getElementById('edit_item_barang_id');
        editSelect.value = item.barang_id;
        document.getElementById('edit_item_lokasi_spesifik').value = item.lokasi_spesifik || '';
        document.getElementById('edit_item_baik').value = item.jumlah_baik;
        document.getElementById('edit_item_rusak').value = item.jumlah_rusak;
        document.getElementById('edit_item_keterangan').value = item.keterangan || '';

        updateLokasiSpesifikField(editSelect, 'container_edit_item_lokasi_spesifik', 'edit_item_lokasi_spesifik', 'edit_item_lokasi_spesifik_req', 'edit_item_lokasi_spesifik_hint');
        document.getElementById('modalEditItem').classList.remove('hidden');
    }

    function openPhotoModal(imgSrc, assetName) {
        document.getElementById('photoPreviewImg').src = imgSrc;
        document.getElementById('photoPreviewTitle').innerText = `Foto Kerusakan: ${assetName}`;
        document.getElementById('modalPhotoPreview').classList.remove('hidden');
    }

    function closePhotoModal(e) {
        document.getElementById('modalPhotoPreview').classList.add('hidden');
    }
</script>
@endpush

</x-admin-layout>
