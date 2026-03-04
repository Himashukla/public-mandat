<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteTest extends TestCase
{
    use RefreshDatabase;

    private function createPoll(array $pollAttributes = []): Poll
    {
        $admin = User::factory()->create(['user_type' => 'admin']);

        $poll = Poll::create(array_merge([
            'user_id'           => $admin->id,
            'question'          => 'What is your favourite color?',
            'description'       => 'Pick one',
            'is_active'         => true,
            'allow_guest_votes' => true,
            'starts_at'         => null,
            'ends_at'           => null,
        ], $pollAttributes));

        PollOption::create(['poll_id' => $poll->id, 'label' => 'Red',  'position' => 0]);
        PollOption::create(['poll_id' => $poll->id, 'label' => 'Blue', 'position' => 1]);

        return $poll->load('options');
    }

    // test 1: user can vote on an active poll
    public function test_user_can_vote_on_active_poll()
    {
        $poll   = $this->createPoll();
        $option = $poll->options->first();

        $response = $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->postJson(route('frontend.polls.vote', $poll), [
                'option_id' => $option->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Your vote has been recorded!',
            ]);

        $this->assertDatabaseHas('votes', [
            'poll_id'        => $poll->id,
            'poll_option_id' => $option->id,
            'ip_address'     => '1.2.3.4',
        ]);
    }

    // test 2: same IP cannot vote twice on same poll
    public function test_same_ip_cannot_vote_twice()
    {
        $poll   = $this->createPoll();
        $option = $poll->options->first();

        // first vote
        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->postJson(route('frontend.polls.vote', $poll), [
                'option_id' => $option->id,
            ]);

        // second vote from same IP
        $response = $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->postJson(route('frontend.polls.vote', $poll), [
                'option_id' => $option->id,
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'You have already voted on this poll.',
            ]);

        // only one vote should exist
        $this->assertEquals(1, Vote::where('poll_id', $poll->id)->count());
    }

    // test 3: different IPs can vote on same poll
    public function test_different_ips_can_vote_on_same_poll()
    {
        $poll   = $this->createPoll();
        $option = $poll->options->first();

        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->postJson(route('frontend.polls.vote', $poll), [
                'option_id' => $option->id,
            ])->assertStatus(200);

        $this->withServerVariables(['REMOTE_ADDR' => '5.6.7.8'])
            ->postJson(route('frontend.polls.vote', $poll), [
                'option_id' => $option->id,
            ])->assertStatus(200);

        $this->assertEquals(2, Vote::where('poll_id', $poll->id)->count());
    }

    // test 4: cannot vote on closed poll
    public function test_cannot_vote_on_closed_poll()
    {
        $poll   = $this->createPoll(['is_active' => false]);
        $option = $poll->options->first();

        $response = $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->postJson(route('frontend.polls.vote', $poll), [
                'option_id' => $option->id,
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'This poll is no longer active.',
            ]);

        $this->assertEquals(0, Vote::where('poll_id', $poll->id)->count());
    }

    // test 5: cannot vote on expired poll
    public function test_cannot_vote_on_expired_poll()
    {
        $poll   = $this->createPoll(['ends_at' => now()->subDay()]);
        $option = $poll->options->first();

        $response = $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->postJson(route('frontend.polls.vote', $poll), [
                'option_id' => $option->id,
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'This poll has expired.',
            ]);

        $this->assertEquals(0, Vote::where('poll_id', $poll->id)->count());
    }

    // test 6: vote count updates correctly after voting
    public function test_vote_count_updates_after_voting()
    {
        $poll    = $this->createPoll();
        $option1 = $poll->options->first();
        $option2 = $poll->options->last();

        $this->withServerVariables(['REMOTE_ADDR' => '1.1.1.1'])
            ->postJson(route('frontend.polls.vote', $poll), [
                'option_id' => $option1->id,
            ]);

        $this->withServerVariables(['REMOTE_ADDR' => '2.2.2.2'])
            ->postJson(route('frontend.polls.vote', $poll), [
                'option_id' => $option1->id,
            ]);

        $this->withServerVariables(['REMOTE_ADDR' => '3.3.3.3'])
            ->postJson(route('frontend.polls.vote', $poll), [
                'option_id' => $option2->id,
            ]);

        $this->assertEquals(2, Vote::where('poll_option_id', $option1->id)->count());
        $this->assertEquals(1, Vote::where('poll_option_id', $option2->id)->count());
        $this->assertEquals(3, Vote::where('poll_id', $poll->id)->count());
    }
}