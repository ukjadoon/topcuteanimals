<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class CreateSitemap extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
        $count = 0;
        $total = 0;
        \App\Post::whereNotNull('cloudinary_url')
            ->chunk(1000, function ($posts) use (&$count, &$total) {
                $count += 1;
                $total += 1;
                $sitemapPosts = \App::make('sitemap');
                foreach ($posts as $post) {
                    $sitemapPosts->add(\URL::to($post->slug), $post->updated_at, 0.5, 'weekly');
                }
                $sitemapPosts->store('xml', 'sitemap-posts-' . $count);
            });

        $sitemap = \App::make('sitemap');
        for ($i = 1; $i <= $count; $i++)
        {
            $sitemap->addSitemap(\URL::to('sitemap-posts-' . $i . '.xml'));
        }
        \Log::info('Create sitemap queue command added ' . $total . ' links to the sitemap');
        $sitemap->store('sitemapindex', 'sitemap');
	}

}
