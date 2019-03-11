<!DOCTYPE html>
<html class="" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <title>{{ config('app.name', 'Eat Local ICT') }}</title>
    <meta name="description" content="Eat Local in Wichita, KS. Use this web app to find a locally-owned and operate restaurant, bar, or cafe in Wichita.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/fav/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/fav/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/fav/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/fav/favicon-16x16.png">
    <link rel="manifest" href="/fav/site.webmanifest">
    <link rel="mask-icon" href="/fav/safari-pinned-tab.svg" color="#292929">
    <link rel="shortcut icon" href="/fav/favicon.ico">
    <meta name="msapplication-TileColor" content="#292929">
    <meta name="msapplication-config" content="/fav/browserconfig.xml">
    <meta name="theme-color" content="#292929">


    <link rel="stylesheet" href="{{ mix('/css/app.css') }}" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/solid.css" integrity="sha384-r/k8YTFqmlOaqRkZuSiE9trsrDXkh07mRaoGBMoDcmA58OHILZPsk29i2BsFng1B" crossorigin="anonymous">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/fontawesome.css" integrity="sha384-4aon80D8rXCGx9ayDt85LbyUHeMWd3UiBaWliBlJ53yzm9hqN21A+o1pqoyK04h+" crossorigin="anonymous">


    <base href="/">
    <script>
        window.Laravel = {!! $token!!};
    </script>
</head>
<body>
<noscript>
    This is your fallback content in case JavaScript fails to load.
</noscript>
<div id="app">
    <div v-if="false" style="background-color: #292929;position: fixed;z-index:10000000000;top:0;bottom: 0;left: 0;right:0;">
        <p style="text-align: center;color:white;font-size: 25px;padding-top: 30vh">Eat Local ICT Is Loading...Please Wait</p>
    </div>
    <nav class="navbar is-fixed-top is-translucent" role="navigation" aria-label="main navigation">
  <div class="container">
      <div class="navbar-brand">


          <router-link :to="{name:'home'}"  class="navbar-item has-text-white">
              <img src="/img/logo.svg" alt="EatLocalICT" width="42" height="34">&nbsp;<strong>EatLocalICT</strong>
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
                <strong>EatLocalICT</strong> by <a href="//instagram.com/veloace" target="_blank" rel="nofollow">VeloAce</a>.
            </p>
            <p>Want to help out? Check out our stuff on <a href="https://teespring.com/eat-local-ict#pid=76&cid=5845&sid=front" target="_blank" rel="nofollow">Teespring</a>
                or buy us a coffee on <a href="https://ko-fi.com/veloace" target="_blank" rel="nofollow">Ko-Fi</a>.
            </p>
            <p>
                <img src="/img/logo.svg" alt="EatLocalICT" width="102" height="68">
            </p>
            <p>
                Copyright &copy; 2018-2019. View our repository <a href="https://github.com/veloace/eat-local-ict" target="_blank" rel="nofollow">on Github</a>.
            </p>
            </p>
        </div>
    </footer>

    <div v-if="notification.show" class="notification notification-fixed-bottom has-text-centered" :class="notification.type">
        <button class="delete" @click="notification.show = !notification.show"></button>
        @{{notification.message}}
    </div>

    <b-modal :active.sync="descriptionSuggestion.show">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <h2 class="modal-card-title">Suggest a Description</h2>
            </header>
            <section class="modal-card-body has-text-white">
                <label class="heading has-text-white" for="description-suggestion">Suggest a Description for <strong class="has-text-weight-bold has-text-white">@{{descriptionSuggestion.name}}</strong></label>
                <textarea id="description-suggestion" class="textarea" v-model="descriptionSuggestion.description" :placeholder="'How would you describe \''+descriptionSuggestion.name+'\'?'">
                        </textarea>

            </section>
            <footer class="modal-card-foot">
                <button @click="submitDescriptionSuggestion" class="button is-small is-success">Submit Suggestion</button>
                <button class="button is-danger is-small" @click="descriptionSuggestion.show=false">Cancel</button>
            </footer>
        </div>
        <button class="modal-close is-large" aria-label="close" @click="descriptionSuggestion.show=false"></button>
    </b-modal>
</div>
<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
