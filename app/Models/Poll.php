<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Poll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'question',
        'description',
        'is_active',
        'allow_guest_votes',
        'starts_at',
        'ends_at',
        'is_multiple_choice', // future field
        'max_votes_per_user', // future field
        'results_visibility'  // future field
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'allow_guest_votes' => 'boolean',
        'starts_at'         => 'datetime',
        'ends_at'           => 'datetime',
    ];

    // -----------------------------------------------
    // Relationships
    // -----------------------------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function options()
    {
        return $this->hasMany(PollOption::class)->orderBy('position');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // -----------------------------------------------
    // Scopes
    // -----------------------------------------------

    /** Only active polls */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Only closed polls */
    public function scopeClosed($query)
    {
        return $query->where('is_active', false);
    }

    // -----------------------------------------------
    // Helpers
    // -----------------------------------------------

    public function totalVotes(): int
    {
        return $this->votes()->count();
    }

    public function hasUserVoted(int $userId): bool
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function hasIpVoted(string $ip): bool
    {
        return $this->votes()->where('ip_address', $ip)->exists();
    }

    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function shareableLink(): string
    {
        return route('polls.show', $this);
    }
}