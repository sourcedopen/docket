<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index(): View
    {
        $activities = Activity::query()
            ->with(['subject', 'causer'])
            ->latest()
            ->paginate(50);

        return view('activity.index', compact('activities'));
    }
}
