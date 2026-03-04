<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'user_id',
        'ip_address',
        'session_id',
        'user_agent',  // future field
        'country',     // future field
        'is_flagged',  // future field
    ];

    // -----------------------------------------------
    // Relationships
    // -----------------------------------------------

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function pollOption()
    {
        return $this->belongsTo(PollOption::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // -----------------------------------------------
    // Helpers
    // -----------------------------------------------

    public function isGuest(): bool
    {
        return is_null($this->user_id);
    }
}