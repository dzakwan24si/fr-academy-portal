<?php

namespace App\Http\Controllers\Admin;

class NonRegulerWeeklyReportController extends WeeklyReportController
{
    protected string $viewPrefix = 'admin.non-reguler.weekly_reports.';
    protected string $routeIndex = 'training-non-reguler.weekly-reports.index';
}
