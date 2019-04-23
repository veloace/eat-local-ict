
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
import InvisibleRecaptcha from 'vue-invisible-recaptcha';

window.Vue = require('vue');
window.Vue.use(VueRouter);
window.Vue.use(Buefy);
window.Vue.use(VueAnalytics, {
    id: process.env.MIX_GOOGLE_ANALYTCS_SITE,
    checkDuplicatedScript: true,
    router
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('login', require('./components/Login.vue'));
Vue.component('registration', require('./components/Register.vue'));
Vue.component('description-suggestion', require('./components/DescriptionSuggestion.vue'));
Vue.component('legal-info', require('./components/LegalInfo.vue'));
Vue.component('invisible-recaptcha', InvisibleRecaptcha);

const app = new Vue({
    el: '#app',
    router,
    data(){
        return{
            recaptcha:process.env.MIX_RECAPTCHA,
            descriptionSuggestion:{
                show:false,
                name:null,
                id:null,
                description:null
            },
            loading:false,
            geo:{//center of wichita is default value with timestamp of 1000 seconds in the past
                lat:37.6789,
                lng:-97.3420,
                timestamp:(Date.now()-10)
            },
            user:{
                name:null,
                logged:false
            },
            showLoginModal:false,
            showRegistrationModal:false,
            legalInfo:null,
            showLegalInfo:false

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
                    type='is-'+type;
                    break;
                default:
                    type='is-info';
            }//type determination

            this.$notification.open({
                duration: 5000,
                message: message,
                position: 'is-top-right',
                type: type,
                hasIcon: true,
                iconPack:'fa'
            })

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
         * Attempt to get the user's location from the navigation.geolocation API
         */
        getUserLocation()
        {

            //only reset the user location if the data is more than 10 minutes old.
            if((Date.now()-this.geo.timestamp) >=1000)
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
        },//setGeo

        isAuthenticated(redirectIfAuthenticated = false,redirectIfNotAuthenticated=false,route=null)
        {
            this.loading= true;
            route = route===null ? 'home':route;
            axios.get('/api/user')
                .then((response) => {
                    this.loading= false;
                    if (response.status===200)
                    {
                        this.user.name= response.data.name;
                        this.user.logged= true;
                        if(redirectIfAuthenticated)
                        {
                            this.$router.push({name:route});
                        }
                    }
                    else if (response.status===204)
                    {
                        this.user = {
                            name:null,
                            logged:false
                        };//set to default values
                        this.showLoginModal=false;
                        if(redirectIfNotAuthenticated)
                        {
                            this.$router.push({name:route});
                        }

                    }
                })
                .catch((error) => {
                    this.loading= false;
                    this.user = {
                        name:null,
                        logged:false
                    };//set to default values
                    this.showLoginModal=false;
                    if(redirectIfNotAuthenticated)
                    {
                        this.$router.push({name:route});
                    }
                });
        },
        toggleLegalInfoModal(type=false)
        {
            if(type)
            {
                this.legalInfo=type;
                this.showLegalInfo=true;
            }
            else
            {
                this.legalInfo=null;
                this.showLegalInfo=false;
            }
        },
        logout()
        {
            this.loading = true;
            axios.post('4236a440a662cc8253d7536e5aa17942')
                .then((response) => {
                    this.loading = false;
                    this.showNotification('You have been logged out.','success');
                    this.user={
                        name:null,
                        logged:false
                    };
                })
                .catch((error) => {
                //todo: something

                    this.loading = false;

                });
            this.$router.push({name:'home'})
        }

    },//methods

    /**
     * Start getting the user's location as soon as possible.
     */
    created(){
        this.isAuthenticated();
        this.getUserLocation();
    }
});
