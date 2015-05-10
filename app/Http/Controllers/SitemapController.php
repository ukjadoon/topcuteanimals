<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class SitemapController extends Controller {

    public function handleSitemap ()
    {
        \Queue::push(new \App\Commands\CreateSitemap());

        return 'done ' . date('Y-M-D H:i:s');
    }

}
