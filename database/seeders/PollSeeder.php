<?php

namespace Database\Seeders;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Database\Seeder;

class PollSeeder extends Seeder
{
    public function run(): void
    {
        $polls = [
            [
                'question' => 'If your pet could talk, what would they say first?',
                'description' => 'We all know they have opinions.',
                'options' => [
                    'Feed me. Now.',
                    'Who is this stranger you call a vet?',
                    'I saw what you did last night.',
                    'Can we go for a walk... again?',
                ],
            ],
            [
                'question' => 'What is the real reason you open the fridge 10 times a day?',
                'description' => 'Be honest. No judgment here.',
                'options' => [
                    'Maybe something new appeared',
                    'I forget what I was looking for',
                    'It is basically meditation',
                    'The cold air feels nice',
                ],
            ],
            [
                'question' => 'You have 5 minutes before a guest arrives. What do you clean first?',
                'description' => 'Priorities reveal character.',
                'options' => [
                    'Shove everything under the bed',
                    'Wipe the kitchen counter only',
                    'Light a candle and hope for the best',
                    'Pretend you just got home too',
                ],
            ],
            [
                'question' => 'Which superpower would actually ruin your life?',
                'description' => 'Think carefully before you answer.',
                'options' => [
                    'Reading minds (you cannot unread them)',
                    'Invisibility (but your clothes are not invisible)',
                    'Time travel (you will mess something up)',
                    'Flying (have you seen the weather)',
                ],
            ],
            [
                'question' => 'What do you do when a song comes on that you love but you are in public?',
                'description' => 'We all have a move.',
                'options' => [
                    'Subtle head nod only',
                    'Full concert mode, no regrets',
                    'Mouth the words with intense eye contact',
                    'Pretend I am not affected but I am deeply affected',
                ],
            ],
        ];

        foreach ($polls as $data) {
            $poll = Poll::create([
                'user_id'           => 1,
                'question'          => $data['question'],
                'description'       => $data['description'],
                'is_active'         => true,
                'allow_guest_votes' => true,
                'starts_at'         => null,
                'ends_at'           => null,
            ]);

            foreach ($data['options'] as $index => $label) {
                PollOption::create([
                    'poll_id'  => $poll->id,
                    'label'    => $label,
                    'position' => $index,
                ]);
            }
        }
    }
}