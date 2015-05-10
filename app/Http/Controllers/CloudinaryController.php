<?php namespace App\Http\Controllers;

class CloudinaryController extends Controller {

    public function __construct()
    {

    }
    public function handleCloudinary()
    {
        $posts = \App\Post::whereNull('cloudinary_url')->get();
        foreach ($posts as $post) {
            \Queue::push(new \App\Commands\UploadToCloudinary($post));
        }

        return 'done ' . date('Y-M-D H:i:s');
    }
}