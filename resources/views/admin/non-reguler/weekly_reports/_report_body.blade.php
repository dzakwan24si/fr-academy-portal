<!-- Action Bar -->
<div class="max-w-[210mm] mx-auto mb-6 flex justify-between items-center no-print bg-white p-4 rounded-xl shadow-sm">
    <a href="{{ route('training-non-reguler.weekly-reports.index') }}" class="text-gray-600 font-semibold hover:text-gray-900 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Daftar
    </a>
    <div class="flex gap-3">
        <button onclick="window.print()" class="px-5 py-2 bg-blue-600 text-white font-bold rounded-lg shadow-sm hover:bg-blue-700 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak
        </button>
    </div>
</div>

@if($weeklyReport->is_merged)
    @php
        $originalReports = \App\Models\WeeklyReport::onlyTrashed()
            ->where('parent_report_id', $weeklyReport->id)
            ->orderBy('id', 'asc')
            ->get();
    @endphp

    @if($originalReports->isNotEmpty())
        @foreach($originalReports as $origReport)
            @php
                // Load relations for original report
                $origReport->load([
                    'trainingProgram',
                    'trainer',
                    'trainingSessions.sessionParticipants.trainee',
                    'trainingSessions.evaluations',
                    'trainingSessions.ojtPlacements.trainee',
                    'trainingSessions.photos',
                    'quizScores.trainee',
                    'rankings.trainee',
                    'resignations.trainee'
                ]);
            @endphp
            @include('admin.non-reguler.weekly_reports._report_body_single', ['weeklyReport' => $origReport])
            
            @if(!$loop->last)
                <div style="page-break-after: always; clear: both; break-after: page;"></div>
            @endif
        @endforeach
    @else
        <!-- Fallback jika data aslinya tidak ditemukan -->
        @include('admin.non-reguler.weekly_reports._report_body_single', ['weeklyReport' => $weeklyReport])
    @endif
@else
    @include('admin.non-reguler.weekly_reports._report_body_single', ['weeklyReport' => $weeklyReport])
@endif
