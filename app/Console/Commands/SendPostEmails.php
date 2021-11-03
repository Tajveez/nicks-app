<?php

namespace App\Console\Commands;

use App\Models\Post;
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
        return Command::SUCCESS;
    }
}
