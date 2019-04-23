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

                            <b-field label="Password" custom-class="has-text-white" :type="errors.password ?'is-danger':''" :message="errors.password">
                                <b-input v-model="password" type="password" placeholder="Password" autofocus></b-input>
                            </b-field>

                            <b-field label="Re-Enter Password" custom-class="has-text-white" :type="errors.password_confirmation ?'is-danger':''" :message="errors.password_confirmation">
                                <b-input v-model="password_confirmation"  type="password" placeholder="Confirm Password" autofocus></b-input>
                            </b-field>




                        </div>
                        <footer class="card-footer has-background-black-ter">
                            <p class="card-footer-item">

                                <button class="button is-success" @click="resetPassword">Reset Password</button>
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
                password:null,
                password_confirmation:null,
                token:null,
                errors:{
                    email:null,
                    password:null,
                    password_confirmation:null,
                    token:null,
                }
            }
        },
        methods: {

            resetPassword()
            {
                axios.post('ad7dc0e8839444523808953373d057581',{
                    email:this.email,
                    password:this.password,
                    password_confirmation:this.password_confirmation,
                    token:this.token,
                })
                    .then((response)=>{


                    })
                    .catch((error) => {
                        if (error.response.status===422)
                        {
                            if(error.response.data.message && !error.response.data.errors)
                            {
                                this.$root.showNotification('PASSWORD RESET FAILED. We could not find a user with that email and password. Please try again.','warning');
                                this.password=null;
                            }
                            else
                            {
                                let errors = error.response.data.errors;
                                for (const [key, value] of Object.entries(errors)) {
                                    this.errors[key] = value[0];
                                }
                            }

                        }
            })
            }
        },
        activated()
        {
            this.$root.showLoginModal = false;
            this.$root.isAuthenticated(true);
            this.token = this.$route.params.token;
        }
        }
</script>