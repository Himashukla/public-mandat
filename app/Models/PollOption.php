<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'label',
        'position',
        'color',
        'image' // future field
    ];

    // -----------------------------------------------
    // Relationships
    // -----------------------------------------------

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function totalVotes(): int
    {
        return $this->votes()->count();
    }

    // -----------------------------------------------
    // Helpers
    // -----------------------------------------------

    /** Percentage of votes this option received (For frontend) */
    public function percentage(): float
    {
        $total = $this->poll->totalVotes();

        if ($total === 0) {
            return 0;
        }

        return round(($this->totalVotes() / $total) * 100, 1);
    }
}