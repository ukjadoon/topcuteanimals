<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;


class UploadToCloudinary extends Command implements SelfHandling, ShouldBeQueued {

    /** @var \App\Post  */
    protected $post;

    /**
     * Create a new Command instance
     *
     * @param \App\Post $post
     * @param $link
     */
	public function __construct(\App\Post $post)
	{
        // Configure Cloudinary
        \Cloudinary::config(array(
            "cloud_name" => env('APP_CLOUDINARY_NAME'),
            "api_key" => env('APP_CLOUDINARY_KEY'),
            "api_secret" => env('APP_CLOUDINARY_SECRET')
        ));
        $this->post = $post;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
        try {
            $cloudinary = \Cloudinary\Uploader::upload($this->post->url, ['quality' => 70, 'width' => 1024, 'crop' => 'limit']);
            $cloudinaryUrl = $cloudinary['url'];
            $this->post->cloudinary_url = $cloudinaryUrl;
            $this->post->save();
            \Log::info('Upload to Cloudinary command uploaded image for post id = ' .$this->post->id);
        } catch (\Exception $e) {
            $this->post->delete();
        }

	}

}
