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
    <script src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/solid.css" integrity="sha384-r/k8YTFqmlOaqRkZuSiE9trsrDXkh07mRaoGBMoDcmA58OHILZPsk29i2BsFng1B" crossorigin="anonymous">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/fontawesome.css" integrity="sha384-4aon80D8rXCGx9ayDt85LbyUHeMWd3UiBaWliBlJ53yzm9hqN21A+o1pqoyK04h+" crossorigin="anonymous">


    <base href="/">
    <script>
        window.Laravel = {!! $token!!};
        window.tags = {!! $tags!!};
        @if (session('message'))
              window.message =  "{!!  session('message') !!}";
        @endif
    </script>
</head>
<body>
<noscript>
   <h1>Hi there</h1>
    <p>Please allow JavaScript :)</p>
</noscript>
<div id="app">
    <b-loading :is-full-page="true" :active.sync="$root.loading" ></b-loading>

    <div v-if="false" style="background-color: #292929;position: fixed;z-index:10000000000;top:0;bottom: 0;left: 0;right:0;">
        <p style="text-align: center;color:white;font-size: 25px;padding-top: 30vh">Eat Local ICT Is Loading...Please Wait</p>
    </div>
    <nav class="navbar is-fixed-top is-translucent" role="navigation" aria-label="main navigation">
  <div class="container">
      <div class="navbar-brand">

          <router-link :to="{name:'home'}"  class="navbar-item has-text-white">
              <img src="/img/logo-title-alt.svg" alt="EatLocalICT" width="42" height="34">&nbsp;<strong>EatLocalICT</strong>
          </router-link>
      </div>
      <div class="navbar-menu">
          <div class="navbar-end" >

              <div class="navbar-item" >
                  <b-dropdown aria-role="list">
                      <a class="has-text-white"  slot="trigger"><i class="fa fa-list"></i><span class="is-hidden-mobile">&nbsp;Lists</span></a>
                      <b-dropdown-item aria-role="listitem" @click="goToProtected('favorites')">Your Favorites</b-dropdown-item>
                      <b-dropdown-item aria-role="listitem" @click="goToProtected('saved')">Saved for Later</b-dropdown-item>
                  </b-dropdown>
              </div>



               <div class="navbar-item" v-if="$root.user.logged">
                      <router-link :to="{name:'account'}"  class="has-text-white"><i class="fa fa-cogs"></i></router-link>
              </div>
              <div class="navbar-item" v-if="!$root.user.logged">
                  <a @click="$root.showLoginModal=true" class="has-text-white"><i class="fa fa-sign-in-alt"></i><span class="is-hidden-mobile">&nbsp;Login</span></a>

              </div>
              <div class="navbar-item" v-else>
                      <a @click="$root.logout" class="has-text-white"><i class="fa fa-sign-out-alt"></i> <span class="is-hidden-mobile">&nbsp;Logout</span>  </a>
              </div>
          </div>
      </div>

  </div>
    </nav>

        <section >
            <keep-alive>
                <router-view class="has-background-black-ter"></router-view>
            </keep-alive>
        </section>

    <footer class="footer has-background-grey-dark has-text-white">
        <div class="content ">

            <p class="has-text-centered">
                EatLocalICT by <a href="//instagram.com/veloace" target="_blank" rel="nofollow">VeloAce</a>. Copyright &copy; 2018-2019. View our repository <a href="https://github.com/veloace/eat-local-ict" target="_blank" rel="nofollow">on Github</a>.

            </p>
            <hr>
            <div class="columns">
                <div class="column is-one-quarter">
                    <h2 class="subtitle has-text-white">Legal Information</h2>
                    <ul>
                        <li><a @click="$root.toggleLegalInfoModal('terms')" >Terms and Conditions</a></li>
                        <li><a @click="$root.toggleLegalInfoModal('cookies')" >Cookie Policy</a></li>
                    </ul>
                </div>
                <div class="column is-one-quarter">
                    <h2 class="subtitle has-text-white">Extras</h2>
                    <ul>
                        <a href='https://ko-fi.com/T6T2KAM0' target='_blank'><img height='36' style='border:0px;height:36px;' src='https://az743702.vo.msecnd.net/cdn/kofi1.png?v=2' border='0' alt='Buy Me a Coffee at ko-fi.com' /></a>
                        <li><router-link :to="{name:'about'}" >About EatLocalICT</router-link></li>
                    </ul>
                </div>
                <div class="column is-half">
                    <h2 class="subtitle has-text-white has-text-centered">Sponsors</h2>
                    <p class="has-text-centered "><span class="has-text-weight-bold ">WE HAVE NO SPONSORS RIGHT NOW.</span> If you are interested in sponsoring EatLocalICT and want to know more about the potential benefits, please <a  href="mailto:sponsors@eatlocalict.com?subject=Sponsorship%20for%20EatLocalICT">Shoot us an Email</a>.</p>
                </div>
            </div>
        </div>
    </footer>

    <login></login>
    <registration></registration>
    <description-suggestion></description-suggestion>
    <legal-info :type="legalInfo" v-if="legalInfo"></legal-info>
</div>
<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
