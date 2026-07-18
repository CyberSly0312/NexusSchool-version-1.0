<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $users = User::where('school_id', $school->id)->take(3)->get();
        if ($users->isEmpty()) {
            return;
        }

        $sender = $users->first();

        $messages = [
            [
                'subject' => 'Welcome to the System',
                'body' => 'Hello! Welcome to the Nexus School communication system. You can use this to send and receive messages.',
                'type' => 'direct',
                'status' => 'sent',
                'sent_at' => now(),
            ],
            [
                'subject' => 'Schedule Update',
                'body' => 'Please note that the class schedule has been updated for next week. Check the timetable for details.',
                'type' => 'broadcast',
                'status' => 'sent',
                'sent_at' => now(),
            ],
            [
                'subject' => 'Meeting Notes',
                'body' => 'Here are the notes from today\'s department meeting. Please review and provide feedback.',
                'type' => 'direct',
                'status' => 'draft',
            ],
        ];

        foreach ($messages as $messageData) {
            $message = Message::firstOrCreate(
                ['school_id' => $school->id, 'subject' => $messageData['subject']],
                array_merge($messageData, [
                    'school_id' => $school->id,
                    'sender_id' => $sender->id,
                ])
            );

            foreach ($users->slice(1) as $recipient) {
                MessageRecipient::firstOrCreate(
                    ['message_id' => $message->id, 'recipient_id' => $recipient->id],
                    [
                        'school_id' => $school->id,
                        'status' => 'delivered',
                    ]
                );
            }
        }
    }
}
