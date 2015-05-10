@extends('master')

@section('content')
<div class="pure-g" style="text-align: center; margin-top: 80px;">
    <div class="pure-u-1" style="width: 100%; overflow-x: hidden;">
        @if (strpos($post->cloudinary_url, '.gif') === false)
            <img class="pure-img-responsive" src="{{ str_replace('png', 'jpg', $post->cloudinary_url) }}" alt="{{ $post->headline }}" />
        @else
            <video autoplay loop muted="muted" poster="{{ str_replace('gif', 'jpg', $post->cloudinary_url) }}"><source type="video/mp4" src="{{ str_replace('gif', 'mp4', $post->cloudinary_url)  }}"> </video>
        @endif
    </div>
    <div class="pure-u-1">
        <h2>{{ $post->headline }}</h2>
    </div>
    <div class="pure-u-1">
        <a href="https://twitter.com/share" class="twitter-share-button" data-via="topcuteanimals">Tweet</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
    </div>
    <div class="pure-u-1">
            <div class="fb-like" data-href="https://www.facebook.com/pages/Top-Cute-Animals/974701695875736" data-layout="standard" data-action="like" data-show-faces="false" data-share="true" data-width="250px"></div>
    </div>
</div>
@endsection
