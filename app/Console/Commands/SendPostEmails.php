<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Mail\SubMail;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendPostEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriber:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send post emails to subscribers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // get posts that aren't sent yet
        $posts = DB::table('posts')->where('is_sent', false)->get();
        foreach ($posts as $post) {
            $websiteId = $posts->website_id;
            $subscribers = $this->getAllSubscribers($websiteId);

            $this->sendEmail($subscribers, [
                'post_title' => $post->title,
                'post_description' => $post->description,
            ]);

            $post->update(['is_sent' => true]);
        }

        return Command::SUCCESS;
    }

    public function getAllSubscribers($websiteId)
    {
        $websiteSubscribers = DB::table('subscriptions')->where('website_id', $websiteId)->get();
        return $websiteSubscribers;
    }

    public function sendEmail($subscribers, $content)
    {
        foreach ($subscribers as $sub) {
            $userData = User::get($sub->user_id);
            $email = $userData->email;

            Mail::to($email)->queue(new SubMail($content));
        }
    }
}
