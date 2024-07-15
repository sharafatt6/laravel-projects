<?php

namespace App\Console\Commands;

use App\Models\Posts;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteOldPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now()->subDay();

        $this->info("Deleting posts older than one day...");

        Posts::where('created_at', '<', $date)->chunkById(100, function ($posts) {
            foreach ($posts as $post) {
                if ($post->media) {
                    foreach ($post->media as $key => $file) {
                        $file_path = public_path('post_media/'.$file->file);
                        if(File::exists($file_path)){
                            File::delete($file_path); 
                        }      
                  }
                }
                $post->delete();
            }
        });
        
    }
}
