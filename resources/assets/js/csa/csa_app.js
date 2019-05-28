require('./../bootstrap');
import VueRouter from 'vue-router';
import router  from './csa_router';
import Buefy from 'buefy'

window.Vue = require('vue');
window.Vue.use(VueRouter);
window.Vue.use(Buefy);


const app = new Vue({
    el: '#app',
    router,

    data() {
        return {
            loading:false,
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
        },
    }
});