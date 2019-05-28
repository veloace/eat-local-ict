import VueRouter from 'vue-router';
const routes =
    [
        {
            path: '/',
            component: require('./views/Dashboard.vue'),
            name: 'home',
            alias:['/home','/dash','/dashboard']
        },
        {
            path: '/claims',
            component: require('./views/Ownership.vue'),
            name: 'claims'
        },
        {
            path: '/404',
            component: require('./views/404.vue'),
            name:'404'
        },
        {
            path: '*',
            redirect: '/404'
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