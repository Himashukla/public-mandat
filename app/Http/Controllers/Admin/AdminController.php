<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\Vote;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [  // <-- must be view(), not a string
            'totalPolls'  => Poll::count(),
            'activePolls' => Poll::where('is_active', true)->count(),
            'closedPolls' => Poll::where('is_active', false)->count(),
            'totalVotes'  => Vote::count(),
            'recentPolls' => Poll::withCount('votes')->latest()->take(6)->get(),
            'topPolls'    => Poll::withCount('votes')->orderByDesc('votes_count')->take(5)->get(),
            'recentVotes' => Vote::with(['pollOption.poll', 'user'])->latest()->take(7)->get(),
        ]);
    }
}