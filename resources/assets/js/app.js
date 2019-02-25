
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');
import VueRouter from 'vue-router';
import router  from './router';
import VueAnalytics from 'vue-analytics'

window.Vue = require('vue');
window.Vue.use(VueRouter);
window.Vue.use(VueAnalytics, {
    id: 'UA-65860722-2',
    checkDuplicatedScript: true,
    router
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app',
    router,
    data(){
        return{
            notification:{
                show:false,
                type:'is-info',
                message:''
            },
            descriptionSuggestion:{
                show:false,
                name:null,
                id:null,
                description:null
            },
            loading:false,
            geo:{
                lat:null,
                lng:null,
                timestamp:0
            }

        }
    },
    methods: {
        showNotification(message,type=null)
        {
            switch(type)
            {
                case 'primary':
                case 'link':
                case 'info':
                case 'success':
                case 'warning':
                case 'danger':
                    this.notification.type='is-'+type;
                    break;
                default:
                    this.notification.type='is-info';
            }//type determination

            this.notification.message=message;
            this.notification.show=true;

            //Code to auto-hide notification after 10 seconds
            let self=this;
            setTimeout(function()
            {

                self.notification.show=false;
                self.notification.type='is-info';
                self.notification.message=null;

            }, 10000);


        },//showNotification
        showDescriptionSuggestionModal(id,title){
            this.descriptionSuggestion.show=true;
            this.descriptionSuggestion.id=id;
            this.descriptionSuggestion.name=title;
        },
        submitDescriptionSuggestion()
        {
            this.loading=true;
            axios.post('/api/places/description',{
                id:this.descriptionSuggestion.id,
                description:this.descriptionSuggestion.description,
            })
                .then((response) => {
                    this.loading = false;

                })
                .catch((error) => {
                    this.loading = false;

                });

            /*always do this, we really don't care about the response from the server as the user shouldn't have to be
            worried about it--and there is nothing they can do if it fails, either.
             */
            this.descriptionSuggestion={
                show:false,
                name:null,
                id:null,
                description:null
            };
            this.showNotification('Thanks, we appreciate people like you! We have received your suggestion and will review its suitability for use on EatLocalICT! ','success')
        },
        getUserLocation()
        {

            let options = {
                enableHighAccuracy: true,
                timeout: 5000
            };

            if(navigator.geolocation)
            {
                navigator.geolocation.getCurrentPosition(this.setGeo, function(error){
                }, options);

            }

        },
        setGeo(position)
        {
            console.log('got here');
            let crd = position.coords;
            this.geo.lat = crd.latitude;
            this.geo.lng =crd.longitude;
            this.geo.timestamp =crd.timestamp;
            //
        },
    }
});
