<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function index(Request $request)
    {
        $polls = Poll::with(['user', 'options.votes'])
                    ->withCount('votes')
                    ->latest()
                    ->paginate(9);

        $polls->each(function ($poll) {
            $poll->userHasVoted = $poll->hasIpVoted(request()->ip());
        });

        return view('frontend.index', compact('polls'));
    }

    public function vote(Request $request, Poll $poll)
    {
        \Log::info('Vote function called for poll: ' . $poll->id);

        $request->validate([
            'option_id' => 'required|exists:poll_options,id',
        ]);

        if (!$poll->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This poll is no longer active.'
            ], 403);
        }

        if ($poll->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'This poll has expired.'
            ], 403);
        }

        $option = $poll->options()->find($request->option_id);

        if (!$option) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid option selected.'
            ], 422);
        }

        if ($poll->hasIpVoted(request()->ip())) {
            return response()->json([
                'success' => false,
                'message' => 'You have already voted on this poll.'
            ], 403);
        }

        $poll->votes()->create([
            'poll_option_id' => $option->id,
            'user_id'        => null,
            'ip_address'     => request()->ip(),
            'session_id'     => session()->getId(),
        ]);

        $poll->load('options.votes');

        \Log::info('Broadcasting VoteRecorded event for poll: ' . $poll->id);

        broadcast(new \App\Events\VoteRecorded($poll));

        \Log::info('Broadcast done');

        $results = $poll->options->map(function ($opt) {
            return [
                'id'         => $opt->id,
                'label'      => $opt->label,
                'votes'      => $opt->totalVotes(),
                'percentage' => $opt->percentage(),
            ];
        });

        return response()->json([
            'success'     => true,
            'message'     => 'Your vote has been recorded!',
            'total_votes' => $poll->totalVotes(),
            'results'     => $results,
        ]);
    }
}