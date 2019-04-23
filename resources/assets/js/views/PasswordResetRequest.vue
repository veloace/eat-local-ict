<template>
    <div class="has-background-translucent top-spacer" style="padding-top: 70px">
       <div class="container">
            <div class="columns">
                <div class="column is-half is-offset-one-quarter">
                    <div class="card">
                        <div class="card-header has-background-black-ter">
                            <h1 class="card-header-title has-text-white is-size-4">Forgot Your Password?</h1>
                        </div>
                        <div class="card-content has-background-grey-darker">
                            <p class="subtitle has-text-white">Enter your email address below and we will send you a link to reset your password.</p>
                            <b-field label="Email" custom-class="has-text-white" :type="errors.email ?'is-danger':''" :message="errors.email">
                                <b-input v-model="email" placeholder="Your Email" autofocus></b-input>
                            </b-field>
                        </div>
                        <footer class="card-footer has-background-black-ter">
                            <p class="card-footer-item">

                                <button class="button is-success" @click="sendResetLink">Reset Password</button>
                            </p>
                        </footer>
                    </div>

                </div>
            </div>
       </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                email:null,
                errors:{
                    email:null
                }
            }
        },
        methods:{
            sendResetLink()
            {
                this.$root.loading = true;
                axios.post('/d7dc0e8839444523808953373d057581',{
                    email:this.email
                })
                    .then((response) => {
                        this.$root.showNotification('Please check your inbox. If there is an account for the email you provided, you will receive an email with instructions on how to reset your password.','success');
                        this.$root.loading = false;
                        this.email=null;
                        this.$router.push({name:'home'})
                    })
                    .catch((error) => {
                            this.$root.loading = false;

                            this.$root.showNotification('Looks like we encountered an error while trying to send you a password reset email. Please try again later, or contact support if the problem persists.','danger');
                    });
            },
            },
        activated()
        {
            this.$root.showLoginModal = false;
            this.$root.isAuthenticated(true);

        }
    }
</script>