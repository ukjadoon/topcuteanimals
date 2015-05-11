<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Support\Str;

class Facebook extends Command implements SelfHandling, ShouldBeQueued
{

    use InteractsWithQueue, SerializesModels;

    protected $facebookPost;

    protected $message;

    protected $image;

    /**
     * @param $facebookPost
     */
    public function __construct($facebookPost)
    {
        $this->facebookPost = $facebookPost;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->facebookPost) {
            $this->createNewFacebookPost();
        } else {
            $this->postNewFacebookPost();
        }
    }

    protected function postNewFacebookPost()
    {
        $post = \App\Post::where('id', '>', $this->facebookPost->last_facebook_id)
            ->where('cloudinary_url', 'not like', '%.gif')
            ->first();
        if (!$post) {
            \Log::error('Cannot post new Facebook post');

            return;
        }
        $this->processPostToFacebook($post);

    }

    /**
     * Creates a new Facebook post if the Facebook table is empty
     */
    protected function createNewFacebookPost()
    {
        $this->facebookPost = new \App\Facebook;
        $post = \App\Post::where('id', '>', 0)->first();
        $this->processPostToFacebook($post);
    }

    protected function buildMessage(\App\Post $post)
    {
        if (strlen($post->headline) > 80) {
            $this->message = Str::limit($post->headline, 80, '...');
            $this->message .= ' ' . env('domain') . $post->slug;
        } else {
            $this->message = $post->headline . ' ' . env('domain') . $post->slug;
        }
        $this->image = $post->cloudinary_url;
    }

    protected function callZapier()
    {
        $this->message = urlencode($this->message);
        $this->image = urlencode($this->image);
        $requestUrl = env('APP_ZAPIER_FACEBOOK_URL') . "?message=$this->message&image=$this->image";
        file_get_contents($requestUrl);
    }

    /**
     * @param $post
     */
    protected function updateLastFacebookId($post)
    {
        $this->facebookPost->last_facebook_id = $post->id;
        $this->facebookPost->save();
    }

    /**
     * @param $post
     */
    protected function processPostToFacebook($post)
    {
        $this->buildMessage($post);
        $this->callZapier();
        $this->updateLastFacebookId($post);
    }

}
