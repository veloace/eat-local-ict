import VueRouter from 'vue-router';
const routes =
    [
        {
            path: '/',
            component: require('./views/Home.vue'),
            name: 'home',
            alias:['/home','/login','/register']
        },
        {
            path: '/listing/:id',
            component: require('./views/Listing.vue'),
            name:'listing'
        },
        {
            path: '/search/',
            component: require('./views/Search.vue'),
            name:'search'
        },
        {
            path: '/backers',
            component: require('./views/Backers.vue'),
            name:'backer'
        },
        {
            path: '/about',
            component: require('./views/About.vue'),
            name:'about'
        },
        {
            path: '/password/reset',
            component: require('./views/PasswordResetRequest.vue'),
            name:'passwordResetRequest'
        },
        {
            path: '/password/reset/:token',
            component: require('./views/PasswordReset.vue'),
            name:'passwordReset'
        },
        {
            path: '/account',
            component: require('./views/Account.vue'),
            name:'account'
        },
        {
            path: '/favorites',
            component: require('./views/Favorites.vue'),
            name:'favorites'
        },
        {
            path: '/saved',
            component: require('./views/Saved.vue'),
            name:'saved'
        },
];


    export default new VueRouter({
    mode: 'history',
    base: '/',
    routes,
    hashbang: false,
    scrollBehavior (to, from, savedPosition) {
        return { x: 0, y: 0 }
    }
});