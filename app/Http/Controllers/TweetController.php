<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Tweet;
use Illuminate\Http\Request;

class TweetController extends Controller {

    public function handleTweet()
    {
        $tweet = \App\Tweet::whereNotNull('id')->first();
        \Queue::push(new \App\Commands\Tweet($tweet));

        return 'done ' . date('Y-M-D H:i:s');
    }

}
