<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$monolog = Log::getMonolog();
$syslog = new \Monolog\Handler\SyslogHandler('papertrail');
$formatter = new \Monolog\Formatter\LineFormatter('%channel%.%level_name%: %message% %extra%');
$syslog->setFormatter($formatter);

$monolog->pushHandler($syslog);

Route::get('/', function () {
    $posts = Cache::remember('recent-posts', 30, function () {
        return App\Post::orderBy('id', 'DESC')
            ->whereNotNull('cloudinary_url')
            ->take(2)->get();
    });
    $post = $posts[0];
    $next = $posts[1]->slug;

    return view('main', ['post' => $post, 'next' => $next]);
});

Route::get('{slug}', function ($slug) {
    $post = Cache::remember('slug-' . $slug, 30, function () use ($slug) {
        return App\Post::where('slug', '=', $slug)->first();
    });
    if (! $post) {
        abort(404);
    }
    $next = Cache::remember('lt-' . $post->id, 30, function () use ($post) {
       return App\Post::where('id', '<', $post->id)
           ->orderBy('id', 'DESC')
           ->whereNotNull('cloudinary_url')->first();
    });

    $key = 'gt-' . $post->id;
    $previous = Cache::remember($key, 30, function () use ($post) {
        $prevId = App\Post::where('id', '>', $post->id)
            ->whereNotNull('cloudinary_url')->min('id');
        if ($prevId) {
            $prev = App\Post::find($prevId);

            return $prev;
        }
        return null;
    });
    if ($previous) {
        $previous = $previous->slug;
    }
    if (!$next) {
        $next = Cache::remember('first', 30, function () use ($post) {
            return App\Post::orderBy('id', 'DESC')
                ->whereNotNull('cloudinary_url')
                ->where('id', '!=', $post->id)
                ->first();
        });
    }
    $next = $next->slug;

    return view('main', ['post' => $post, 'next' => $next, 'previous' => $previous, 'subPageHeadline' => $post->headline]);
});

Route::get('secret-route-to-update-posts', function () {
    $url = env('APP_IMPORT_IO_API_URL');
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url
    ]);
    $json = json_decode(curl_exec($curl), true);
    curl_close($curl);
    $total = 0;
    foreach ($json['results'] as $post) {
        $pattern = '/[\w\-]+\.(jpg|png|jpeg)/';
        if (preg_match($pattern, $post['post_link'])) {
            $title = $post['post_title'];
            $link = $post['post_link'];
            $slug = \Illuminate\Support\Str::slug($title);
            $exists = App\Post::where('slug', '=', $slug)
                ->orWhere('url', $link)
                ->orWhere('headline', $title)
                ->count();

            if (!$exists) {
                $total += 1;
                App\Post::create([
                    'headline' => $title,
                    'url' => $link,
                    'slug' => $slug
                ]);
            }
        } else if (strpos($post['post_link'], 'imgur.com') !== false) {
            $urlencode = urlencode($post['post_link']);
            $url = env('APP_IMPORT_IO_URL') . '&url=' . $urlencode;
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url
            ]);
            $json = json_decode(curl_exec($curl), true);
            curl_close($curl);
            $imageUrl = isset($json['results'][0]['image']) ? $json['results'][0]['image'] : '';
            if (! is_array($imageUrl) && preg_match($pattern, $imageUrl)) {
                $title = $post['post_title'];
                $link = $imageUrl;
                \Log::info('Fetching image from ' . $post['post_link'] . ' and the image is ' . $link);
                $slug = \Illuminate\Support\Str::slug($title);
                $exists = App\Post::where('slug', '=', $slug)
                    ->orWhere('url', $link)
                    ->orWhere('headline', $title)
                    ->count();
                if (!$exists) {
                    $total += 1;
                    App\Post::create([
                        'headline' => $title,
                        'url' => $link,
                        'slug' => $slug
                    ]);
                }
            }
        }
    }

    \Log::info('Update posts command found ' . $total . ' new results');

    return 'done ' . date('Y-M-D H:i:s');
});

Route::get('secret-route-to-update-cloudinary', 'CloudinaryController@handleCloudinary');
Route::get('secret-route-to-tweet', 'TweetController@handleTweet');
Route::get('secret-route-to-post-to-facebook', 'FacebookController@handleFacebook');
Route::get('secret-route-to-update-the-sitemap', 'SitemapController@handleSitemap');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
