<template>
    <div class="hero is-fullheight has-background-translucent">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title has-text-white">
                    Page Not Found
                </h1 >
                <p class="has-text-white">It looks like the page you are looking for doesn't exist. Why not try one of the links below?</p>
                <p>
                    <router-link :to="{name:'home'}"  class="button is-link is-inverted  is-outlined has-text-weight-bold">Go Home</router-link>
                </p>
                <p>
                    <router-link :to="{name:'search'}"  class="button is-link is-inverted  is-outlined has-text-weight-bold">Advanced Search</router-link>
                </p>
            </div>
        </div>
    </div>
</template>
<script>

</script>