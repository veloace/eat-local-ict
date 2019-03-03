
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');
import VueRouter from 'vue-router';
import router  from './router';
import Buefy from 'buefy'
import VueAnalytics from 'vue-analytics'

window.Vue = require('vue');
window.Vue.use(VueRouter);
window.Vue.use(Buefy);
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
        /**
         * shows a notification on the bottom of the page, the toggles the  visibility after 10-seconds
         * @param message the message to display to the user
         * @param type the bulma CSS class (primary, link, info, success, warning, danger) to change the notification color.
         */
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
        /**
         * Shows the modal for the user to enter a suggestion for a location that does not have one yet
         * @param id the id of the place model
         * @param title the title of the place model we are updating
         */
        showDescriptionSuggestionModal(id,title){
            this.descriptionSuggestion.show=true;
            this.descriptionSuggestion.id=id;
            this.descriptionSuggestion.name=title;
        },
        /**
         * let's the user make a suggestion for a place that does not have a description yet
         * this method is used by Place.vue, Listing.vue, and Home.vue
         * basically anywhere a place description is displayed.
         * This function will make the actual API call to our server to save the suggestion and toggle the modal and notification messages
         */
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
        /**
         * Attempt to get the user's location from the navigation.geolocation API
         */
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
        /**
         * set the Geographic coordinates for the user from the Navigator.geolocation request
         * @param position
         */
        setGeo(position)
        {
            let crd = position.coords;
            this.geo.lat = crd.latitude;
            this.geo.lng =crd.longitude;
            this.geo.timestamp =crd.timestamp;
            //
        },
    },
    /**
     * Start getting the user's location as soon as possible.
     */
    created(){
        this.getUserLocation();
    }
});
