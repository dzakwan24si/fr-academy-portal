<?php

namespace App\Http\Controllers;

use App\Services\ProgramCatalogService;
use Illuminate\Http\Request;

class ProgramPublicController extends Controller
{
    /**
     * Redirect to default program (FAT) since catalog page is removed.
     */
    public function index()
    {
        return redirect()->route('program.show', 'fat');
    }

    /**
     * Show dedicated detail & syllabus page for a specific clicked program.
     */
    public function show(string $slug)
    {
        $program = ProgramCatalogService::getProgramBySlug($slug);

        if (!$program) {
            abort(404, 'Program tidak ditemukan.');
        }

        $allPrograms = ProgramCatalogService::getAllPrograms();

        return view('program_detail', compact('program', 'allPrograms', 'slug'));
    }
}
