import VueRouter from 'vue-router';
const routes =
    [
        {
            path: '/',
            component: require('./views/Home.vue'),
            name: 'home'
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