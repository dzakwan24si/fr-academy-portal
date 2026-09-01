<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter_category = $request->input('category');
        $filter_status = $request->input('status');

        $articles = Article::with(['category', 'author'])
            ->when($search, function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->when($filter_category, function($q) use ($filter_category) {
                $q->where('category_id', $filter_category);
            })
            ->when($filter_status, function($q) use ($filter_status) {
                $q->where('status', $filter_status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
            
        $categories = Category::all();
            
        return view('admin.article.index', compact('articles', 'search', 'filter_category', 'filter_status', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.article.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|string'
        ]);

        if ($request->has('tags') && !empty($request->tags)) {
            $validated['tags'] = array_filter(array_map('trim', explode(',', $request->tags)));
        } else {
            $validated['tags'] = null;
        }

        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        $validated['author_id'] = auth()->id();
        
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        Article::create($validated);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Article $artikel)
    {
        $categories = Category::all();
        return view('admin.article.form', ['article' => $artikel, 'categories' => $categories]);
    }

    public function update(Request $request, Article $artikel)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|string'
        ]);

        if ($request->has('tags') && !empty($request->tags)) {
            $validated['tags'] = array_filter(array_map('trim', explode(',', $request->tags)));
        } else {
            $validated['tags'] = null;
        }

        if ($request->title !== $artikel->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        }

        if ($validated['status'] === 'published' && !$artikel->published_at) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            if ($artikel->featured_image) {
                Storage::disk('public')->delete($artikel->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        $artikel->update($validated);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $artikel)
    {
        if ($artikel->featured_image) {
            Storage::disk('public')->delete($artikel->featured_image);
        }
        $artikel->delete();
        
        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
