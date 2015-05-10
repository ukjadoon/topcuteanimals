<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class Tweet extends Command implements SelfHandling, ShouldBeQueued
{

    use InteractsWithQueue, SerializesModels;

    protected $tweet;

    protected $message;

    protected $image;

    /**
     * @param $tweet
     */
    public function __construct($tweet)
    {
        $this->tweet = $tweet;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->tweet) {
            $this->createNewTweet();
        } else {
            $this->postNewTweet();
        }
    }

    protected function postNewTweet()
    {
        $post = \App\Post::where('id', '>', $this->tweet->last_tweet_id)
            ->where('cloudinary_url', 'not like', '%.gif')
            ->first();
        if (!$post) {
            \Log::error('Cannot post new tweet');

            return;
        }
        $this->processPostToTweet($post);

    }

    /**
     * Creates a new tweet if the tweet table is empty
     */
    protected function createNewTweet()
    {
        $this->tweet = new \App\Tweet;
        $post = \App\Post::where('id', '>', 0)->first();
        $this->processPostToTweet($post);
    }

    protected function buildMessage(\App\Post $post)
    {
        if (strlen($post->headline) > 80) {
            $messageCut = substr($post->headline, 0, 80);
            $this->message = substr($messageCut, 0, strrpos($messageCut, ' ')) . '...';
            $this->message .= ' http://topcuteanimals.com/' . $post->slug;
        } else {
            $this->message = $post->headline . ' ' . 'http://topcuteanimals.com/' . $post->slug;
        }
        $this->image = $post->cloudinary_url;
    }

    protected function callZapier()
    {
        $this->message = urlencode($this->message);
        $this->image = urlencode($this->image);
        $requestUrl = env('APP_ZAPIER_TWITTER_URL') . "/?message=$this->message&image=$this->image";
        file_get_contents($requestUrl);
    }

    /**
     * @param $post
     */
    protected function updateLastTweetId($post)
    {
        $this->tweet->last_tweet_id = $post->id;
        $this->tweet->save();
    }

    /**
     * @param $post
     */
    protected function processPostToTweet($post)
    {
        $this->buildMessage($post);
        $this->callZapier();
        $this->updateLastTweetId($post);
    }

}
