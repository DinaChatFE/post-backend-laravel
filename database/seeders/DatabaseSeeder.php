<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostInteraction;
use App\Models\User;
use App\Models\UserFollow;
use Error;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    const ALERT_ERROR = "Successfully delete old record from user, post, post interaction, comment, user follow";

    public function run()
    {
        if ($this->command->option('force')) {
            User::truncate();
            Post::truncate();
            PostInteraction::truncate();
            Comment::truncate();
            UserFollow::truncate();
            $this->command->alert(self::ALERT_ERROR);
        }
        DB::beginTransaction();
        try {
            $this->command->alert('Starting dumping data...');
            $randomCount = 50;
            User::factory($randomCount)->create()->each(function ($user) use ($randomCount) {
                $user->posts()->createMany(
                    Post::factory($randomCount * 2)->make()->toArray()
                );
            });
            UserFollow::factory($randomCount * 10)->create();
            PostInteraction::factory($randomCount * 5)->create();
            $this->command->info('Successfully commit all faking data');
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            throw new Error($err->getMessage());
        }
        $this->command->alert('Starting adding comment...');
        try {
            if (!User::count() && !Post::count()) {
                $this->command->alert('There are no user and post on this records');
                return;
            }
            Comment::factory($randomCount * 10)->create();
            Comment::fixTree();
            $this->command->info('Successfully commit comment');
        } catch (Exception $err) {
            throw new Error($err->getMessage());
        }
    }
}
