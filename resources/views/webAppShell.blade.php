<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Skating App') }}</title>
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
    <div v-if="false" style="background-color: #212121;position: absolute;z-index:100000;top:0;bottom: 0;left: 0;right:0;">
        <img style="display:block;margin:auto; padding-top: 30vh" src="/img/ictFlag.svg" height="300">
        <p style="text-align: center;color:white;font-size: 25px;">Eat Local ICT Is Loading...Please Wait</p>
    </div>
    <v-app>
        <transition name="fade">
            <keep-alive>
                <router-view class="filter-content"></router-view>
            </keep-alive>
        </transition>
    </v-app>
</div>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBbSgshuOWaQ8nCyLiOzliH4KFRVLHw1vM&libraries=places"></script>
<script type="text/javascript" src="/js/app.js?ver=0023"></script>
</body>
</html>
