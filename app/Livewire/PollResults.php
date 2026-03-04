<?php

namespace App\Livewire;

use App\Models\Poll;
use Livewire\Component;

class PollResults extends Component
{
    public Poll $poll;

    public function mount(Poll $poll)
    {
        $this->poll = $poll;
    }

    public function render()
    {
        $this->poll->load('options.votes', 'votes');

        return view('components.poll-results', [
            'poll'       => $this->poll,
            'totalVotes' => $this->poll->totalVotes(),
            'options'    => $this->poll->options,
        ]);
    }
}