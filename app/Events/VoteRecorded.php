<?php

namespace App\Events;

use App\Models\Poll;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoteRecorded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(Poll $poll)
    {
        $poll->loadMissing('options.votes');

        $this->payload = [
            'poll'        => ['id' => $poll->id],
            'totalVotes'  => $poll->totalVotes(),
            'results'     => $poll->options->map(function ($opt) {
                return [
                    'id'         => $opt->id,
                    'label'      => $opt->label,
                    'votes'      => $opt->totalVotes(),
                    'percentage' => $opt->percentage(),
                ];
            })->toArray(),
        ];
    }

    public function broadcastOn(): Channel
    {
        return new Channel('polls');
    }

    public function broadcastAs(): string
    {
        return 'vote.recorded';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}