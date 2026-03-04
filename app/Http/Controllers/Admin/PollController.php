<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::where('user_id', auth()->id())
                    ->withCount('votes')
                    ->latest()
                    ->paginate(10);

        return view('admin.polls.index', compact('polls'));
    }

    public function create()
    {
        return view('admin.polls.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question'          => 'required|string|max:255',
            'description'       => 'nullable|string',
            'options'           => 'required|array|min:2',
            'options.*'         => 'required|string|max:255',
            'is_active'         => 'nullable|boolean',
            'allow_guest_votes' => 'nullable|boolean',
            'starts_at'         => 'nullable|date',
            'ends_at'           => 'nullable|date|after:starts_at',
        ]);

        $poll = Poll::create([
            'user_id'           => auth()->id(),
            'question'          => $request->question,
            'description'       => $request->description,
            'is_active'         => $request->boolean('is_active', true),
            'allow_guest_votes' => $request->boolean('allow_guest_votes', true),
            'starts_at'         => $request->starts_at,
            'ends_at'           => $request->ends_at,
        ]);

        foreach ($request->options as $index => $label) {
            PollOption::create([
                'poll_id'  => $poll->id,
                'label'    => $label,
                'position' => $index,
            ]);
        }

        return redirect()->route('admin.polls.index')->with('success', 'Poll created successfully.');
    }

    public function show(Poll $poll)
    {
        $this->authorizeOwner($poll);

        $poll->load('options.votes', 'votes');

        return view('admin.polls.show', compact('poll'));
    }

    public function edit(Poll $poll)
    {
        $this->authorizeOwner($poll);

        $poll->load('options');

        return view('admin.polls.form', compact('poll'));
    }

    public function update(Request $request, Poll $poll)
    {
        $this->authorizeOwner($poll);

        $request->validate([
            'question'          => 'required|string|max:255',
            'description'       => 'nullable|string',
            'options'           => 'required|array|min:2',
            'options.*'         => 'required|string|max:255',
            'is_active'         => 'nullable|boolean',
            'allow_guest_votes' => 'nullable|boolean',
            'starts_at'         => 'nullable|date',
            'ends_at'           => 'nullable|date|after:starts_at',
        ]);

        $poll->update([
            'question'          => $request->question,
            'description'       => $request->description,
            'is_active'         => $request->boolean('is_active'),
            'allow_guest_votes' => $request->boolean('allow_guest_votes'),
            'starts_at'         => $request->starts_at,
            'ends_at'           => $request->ends_at,
        ]);

        // delete old options and re-create
        $poll->options()->delete();

        foreach ($request->options as $index => $label) {
            PollOption::create([
                'poll_id'  => $poll->id,
                'label'    => $label,
                'position' => $index,
            ]);
        }

        return redirect()->route('admin.polls.index')->with('success', 'Poll updated successfully.');
    }

    public function destroy(Poll $poll)
    {
        $this->authorizeOwner($poll);

        $poll->delete();

        return redirect()->route('admin.polls.index')->with('success', 'Poll deleted successfully.');
    }

    private function authorizeOwner(Poll $poll)
    {
        if ($poll->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function toggleStatus(Poll $poll)
    {
        $this->authorizeOwner($poll);

        $poll->update([
            'is_active' => !$poll->is_active
        ]);

        return response()->json([
            'is_active' => $poll->is_active
        ]);
    }
}