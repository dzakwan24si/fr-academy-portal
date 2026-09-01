<?php

use Livewire\Component;
use App\Models\TvPlaylist;
use App\Models\Article;

new class extends Component
{
    public $playlists = [];
    public $articles = [];
    
    public $selectedArticle = '';
    public $duration = 15;

    public function mount()
    {
        $this->loadPlaylists();
        $this->articles = Article::where('status', 'published')->latest()->get();
    }

    public function loadPlaylists()
    {
        $this->playlists = TvPlaylist::with('article')->orderBy('order')->get();
    }

    public function addPlaylist()
    {
        $this->validate([
            'selectedArticle' => 'required|exists:articles,id',
            'duration' => 'required|integer|min:5'
        ]);

        $maxOrder = TvPlaylist::max('order') ?? 0;

        TvPlaylist::create([
            'article_id' => $this->selectedArticle,
            'duration_seconds' => $this->duration,
            'order' => $maxOrder + 1
        ]);

        $this->selectedArticle = '';
        $this->duration = 15;
        $this->loadPlaylists();
        
        session()->flash('success', 'Artikel berhasil ditambahkan ke Playlist.');
    }

    public function removePlaylist($id)
    {
        TvPlaylist::findOrFail($id)->delete();
        $this->loadPlaylists();
        session()->flash('success', 'Item berhasil dihapus dari Playlist.');
    }

    public function updateOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            TvPlaylist::where('id', $id)->update(['order' => $index + 1]);
        }
        $this->loadPlaylists();
    }
};
?>

<div>
    <x-slot name="title">Manajemen Playlist TV</x-slot>
    <x-slot name="header">Playlist TV Display</x-slot>
    <x-slot name="headerSub">Atur urutan tayang informasi di layar TV lobi FR Academy.</x-slot>
    
    <!-- SortableJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg flex">
            <svg class="h-5 w-5 text-green-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Playlist Items (Drag and Drop Area) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Daftar Tayang</h3>
                    <span class="text-xs font-medium bg-teal-100 text-teal-800 px-3 py-1 rounded-full">{{ count($playlists) }} Item</span>
                </div>
                
                <div class="p-4 bg-gray-50 min-h-[400px]">
                    <ul x-data="{
                            initSortable() {
                                new Sortable(this.$refs.sortableList, {
                                    animation: 150,
                                    handle: '.drag-handle',
                                    ghostClass: 'opacity-50',
                                    onEnd: (evt) => {
                                        let orderedIds = Array.from(this.$refs.sortableList.children).map(child => child.dataset.id);
                                        $wire.updateOrder(orderedIds);
                                    }
                                });
                            }
                        }"
                        x-init="initSortable()"
                        x-ref="sortableList"
                        class="space-y-3"
                    >
                        @forelse($playlists as $item)
                            <li data-id="{{ $item->id }}" class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex items-center justify-between group transition">
                                <div class="flex items-center flex-1">
                                    <!-- Drag Handle -->
                                    <div class="drag-handle cursor-grab text-gray-400 hover:text-[#509c38] px-2 py-1 mr-2 transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                                    </div>
                                    
                                    @if($item->article && $item->article->featured_image)
                                        <img src="{{ Storage::url($item->article->featured_image) }}" class="w-16 h-16 rounded-lg object-cover mr-4 border border-gray-200">
                                    @else
                                        <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center mr-4 border border-gray-200 text-gray-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif

                                    <div>
                                        <h4 class="font-bold text-gray-800 line-clamp-1">{{ $item->article->title ?? 'Artikel Telah Dihapus' }}</h4>
                                        <p class="text-xs text-gray-500 mt-1 flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1 text-[#509c38]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Tampil selama {{ $item->duration_seconds }} detik
                                        </p>
                                    </div>
                                </div>
                                
                                <button wire:click="removePlaylist({{ $item->id }})" wire:confirm="Hapus item ini dari playlist?" class="text-gray-400 hover:text-red-500 p-2 rounded-lg hover:bg-red-50 transition opacity-0 group-hover:opacity-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </li>
                        @empty
                            <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <p class="text-sm font-medium">Belum ada item di Playlist.</p>
                                <p class="text-xs">Silakan tambah dari panel di sebelah kanan.</p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>
            
            <div class="mt-4 text-xs text-gray-500 flex items-center">
                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Tahan dan geser ikon baris pada sebelah kiri item untuk mengubah urutan tayang. Perubahan tersimpan secara otomatis.
            </div>
        </div>

        <!-- Add Item Form -->
        <div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sticky top-8">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Tambah ke Playlist</h3>
                
                <form wire:submit="addPlaylist" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Artikel</label>
                        <select wire:model="selectedArticle" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#509c38] text-gray-700 cursor-pointer">
                            <option value="">-- Pilih Artikel --</option>
                            @foreach($articles as $article)
                                <option value="{{ $article->id }}">{{ Str::limit($article->title, 45) }}</option>
                            @endforeach
                        </select>
                        @error('selectedArticle') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Durasi Tayang (Detik)</label>
                        <input type="number" wire:model="duration" min="5" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#509c38] transition"
                            placeholder="Contoh: 15">
                        @error('duration') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#509c38] text-white font-bold rounded-xl hover:bg-green-700 transition shadow-sm flex justify-center items-center">
                        <svg wire:loading.remove wire:target="addPlaylist" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <svg wire:loading wire:target="addPlaylist" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Tambahkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>