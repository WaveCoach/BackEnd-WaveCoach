<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::pluck('id');

        if ($users->isEmpty()) {
            $users = collect([User::factory()->create()->id]);
        }

        $notifications = [
            ['title' => 'New Comment', 'message' => 'Someone commented on your post.'],
            ['title' => 'New Message', 'message' => 'You received a new message.'],
            ['title' => 'System Update', 'message' => 'The system will be under maintenance tonight.'],
            ['title' => 'Reminder', 'message' => 'Don’t forget to check your tasks.'],
            ['title' => 'Welcome', 'message' => 'Welcome to our platform!']
        ];

        foreach ($users as $user_id) {
            foreach ($notifications as $notif) {
                Notification::create([
                    'user_id' => $user_id,
                    'title' => $notif['title'],
                    'message' => $notif['message'],
                    'is_read' => rand(0, 1), // Secara acak, ada yang sudah dibaca, ada yang belum
                    'created_at' => Carbon::now()->subMinutes(rand(1, 120)),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
    }
}
