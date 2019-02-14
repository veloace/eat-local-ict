<!DOCTYPE html>
<html class="" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Eat Local ICT') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
    <!--[if IE]><link rel="shortcut icon" href="/favicons/favicon.ico"><![endif]-->
    <!-- Add to home screen for Android and modern mobile browsers -->
    <link rel="manifest" href="/static/manifest.json">
    <meta name="theme-color" content="#2196f3">
    <link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#2196f3">
    <meta name="apple-mobile-web-app-title" content="Skater.Space">
    <meta name="application-name" content="Skater.Space">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- Add to home screen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Skater.Space">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
    <!-- Add to home screen for Windows -->
    <meta name="msapplication-TileImage" content="/static/img/icons/msapplication-icon-144x144.png">
    <meta name="msapplication-TileColor" content="#000000">
    <link rel="stylesheet" href="/css/app.css" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet" type="text/css">
    <base href="/app">
    <script>
        window.Laravel = {!! $token!!};
        window.tags = {!! $tags !!};
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaInit&render=explicit" async defer>
    </script>
</head>
<body>
<noscript>
    This is your fallback content in case JavaScript fails to load.
</noscript>
<div id="app">
    <div v-if="false" style="background-color: #292929;position: absolute;z-index:10000;top:0;bottom: 0;left: 0;right:0;">
        <p style="text-align: center;color:white;font-size: 25px;padding-top: 30vh">Eat Local ICT Is Loading...Please Wait</p>
    </div>
    <nav class="navbar is-fixed-top is-translucent" role="navigation" aria-label="main navigation">
  <div class="container">
      <div class="navbar-brand">
          <router-link :to="{name:'home'}"  class="navbar-item has-text-white" href="">
              Eat Local ICT
          </router-link>
      </div>
  </div>
    </nav>

        <section class="hero is-fullheight ict-flag-bg">
            <keep-alive>
                <router-view></router-view>
            </keep-alive>
        </section>

    <footer class="footer">
        <div class="content has-text-centered">
            <p>
                <strong>EatLocalICT</strong> by VeloAce. The source code is licensed
                <a href="http://opensource.org/licenses/mit-license.php">MIT</a> and is available <a href="https://github.com/veloace/eat-local-ict" target="_blank" rel="nofollow">on Github</a>. The website content
                is licensed <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/">CC BY NC SA 4.0</a>.
            </p>
            <p><a href="https://ko-fi.com/veloace" target="_blank" rel="nofollow">Support EatLocalICT</a></p>
            </p>
        </div>
    </footer>
</div>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBbSgshuOWaQ8nCyLiOzliH4KFRVLHw1vM&libraries=places"></script>
<script type="text/javascript" src="/js/app.js?ver=0023"></script>
</body>
</html>
