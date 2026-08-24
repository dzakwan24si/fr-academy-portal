<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProgramController extends Controller
{
    /**
     * Display the detailed table view of training programs.
     */
    public function index(Request $request)
    {
        $jsonPath = storage_path('app/kalender_training.json');
        
        $programs = [];
        if (File::exists($jsonPath)) {
            $programs = json_decode(File::get($jsonPath), true);
        }

        // 6 Standard Categories from Pak Yogi
        $categories = [
            'Training Reguler - Staf',
            'Training Reguler - Mandor',
            'Training Riau',
            'Training Kalbar',
            'Training Kaltim',
            'Training Corporate'
        ];

        // Default filters
        $activeCategory = $request->input('category', 'Training Reguler - Staf');
        if (!in_array($activeCategory, $categories)) {
            $activeCategory = 'Training Reguler - Staf';
        }

        $activeYear = '2026'; // Year is locked to 2026
        $search = $request->input('search', '');
        $selectedLocation = $request->input('location', '');

        // Gather all unique locations for filter dropdown
        $allLocations = array_filter(array_unique(array_column($programs, 'location')));
        sort($allLocations);

        // Filter programs for the ACTIVE category
        $filteredPrograms = array_filter($programs, function ($item) use ($activeCategory) {
            return $item['category'] === $activeCategory;
        });

        // Calculate Stats for the active category (before search and location filters are applied)
        $totalPrograms = count($filteredPrograms);
        $totalBatches = array_sum(array_column($filteredPrograms, 'total_batches'));
        
        $totalParticipants = 0;
        foreach ($filteredPrograms as $p) {
            $totalParticipants += $p['target_participants']['total'] ?? 0;
        }

        $stats = [
            'total_programs' => $totalPrograms,
            'total_batches' => $totalBatches,
            'total_participants' => $totalParticipants,
        ];

        // Apply Search & Location Filters on the visible table rows
        if ($search !== '') {
            $filteredPrograms = array_filter($filteredPrograms, function ($item) use ($search) {
                return stripos($item['name'], $search) !== false 
                    || stripos($item['section'], $search) !== false
                    || stripos($item['subsection'], $search) !== false;
            });
        }

        if ($selectedLocation !== '') {
            $filteredPrograms = array_filter($filteredPrograms, function ($item) use ($selectedLocation) {
                return strcasecmp($item['location'], $selectedLocation) === 0;
            });
        }

        return view('Administrasi.kalender_training', [
            'programs' => array_values($filteredPrograms),
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'activeYear' => $activeYear,
            'search' => $search,
            'locations' => $allLocations,
            'selectedLocation' => $selectedLocation,
            'stats' => $stats
        ]);
    }

    /**
     * Display the graphical dashboard summarizing all training programs.
     */
    public function dashboard()
    {
        $jsonPath = storage_path('app/kalender_training.json');
        
        $programs = [];
        if (File::exists($jsonPath)) {
            $programs = json_decode(File::get($jsonPath), true);
        }

        $activeYear = '2026'; // Year is locked to 2026

        // Calculate global statistics across all 112 programs
        $totalPrograms = count($programs);
        $totalBatches = array_sum(array_column($programs, 'total_batches'));
        
        $totalParticipants = 0;
        foreach ($programs as $p) {
            $totalParticipants += $p['target_participants']['total'] ?? 0;
        }

        $stats = [
            'total_programs' => $totalPrograms,
            'total_batches' => $totalBatches,
            'total_participants' => $totalParticipants,
        ];

        return view('Administrasi.dashboard', [
            'programs' => $programs,
            'activeYear' => $activeYear,
            'stats' => $stats
        ]);
    }
}
