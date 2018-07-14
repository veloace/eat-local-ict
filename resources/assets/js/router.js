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
            path: '/search/:term',
            component: require('./views/Search.vue'),
            name:'search'
        }
];


    export default new VueRouter({
    mode: 'history',
    base: '/',
    routes,
    hashbang: false,
});