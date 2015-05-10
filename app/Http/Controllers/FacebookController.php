<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class FacebookController extends Controller {

    public function handleFacebook()
    {
        $facebookPost = \App\Facebook::whereNotNull('id')->first();
        \Queue::push(new \App\Commands\Facebook($facebookPost));

        return 'done ' . date('Y-M-D H:i:s');
    }

}
