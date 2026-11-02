<?php

namespace App\Livewire\Admin;

use App\Models\Article;
use App\Models\TvPlaylist;
use Livewire\Component;

class TvPlaylistManager extends Component
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
            'duration' => 'required|integer|min:5',
        ]);

        $maxOrder = TvPlaylist::max('order') ?? 0;

        TvPlaylist::create([
            'article_id' => $this->selectedArticle,
            'duration_seconds' => $this->duration,
            'order' => $maxOrder + 1,
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

    public function render()
    {
        return view('livewire.admin.tv-playlist-manager');
    }
}
