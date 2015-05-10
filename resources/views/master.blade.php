<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Get your fix of the cutest cats, dogs and other animals on the Internet.">

    <title>{{ isset($subPageHeadline) ? 'TopCuteAnimals.com - ' . $subPageHeadline : 'TopCuteAnimals.com - Get your fix of random cute animals!' }}</title>




<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">



<!--[if lte IE 8]>

    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css">

<![endif]-->
<!--[if gt IE 8]><!-->

    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">

<!--<![endif]-->



<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">




    <!--[if lte IE 8]>
        <link rel="stylesheet" href="css/layouts/marketing-old-ie.css">
    <![endif]-->
    <!--[if gt IE 8]><!-->
        <link rel="stylesheet" href="css/layouts/marketing.css">
    <!--<![endif]-->




</head>
<body>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>








<div class="header">
    <div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
        <a class="pure-menu-heading" href="/">Top Cute Animals</a>
        <ul style="text-align: center; position: relative; left: -10px;" class="pure-menu-list">
            <li>
                @if (isset($previous) && $previous)
                    <a class="pure-button pure-button-primary menu-button" href="/{{ $previous }}" style="font-size: 1.2em; margin-top: 2px;">Prev</a>
                @endif
                <a class="pure-button pure-button-primary menu-button" href="/{{ $next }}" style="font-size: 1.2em; margin-top: 2px;">Next</a>
            </li>
        </ul>
    </div>
</div>


<div class="container" style="margin-top:10px;">
        @yield('content')
</div>
<div class="footer l-box is-center" style="margin-top:10px;">
    Your source for the cutest puppies, kittens and other furry and adorable animals.
</div>

</body>
</html>
