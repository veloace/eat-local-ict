<template>
    <div class="has-background-translucent top-spacer" style="padding-top: 70px">
       <div class="container">
           <div class="modal-card">
               <div class="modal-card-head">
                   <h1 class="modal-card-title">Forgot Your Password?</h1>
               </div>
               <div class="modal-card-body has-background-grey-darker">
                   <p class="subtitle has-text-white">Enter your email address below and we will send you a link to reset your password.</p>
                   <b-field label="Email" custom-class="has-text-white" :type="errors.email ?'is-danger':''" :message="errors.email">
                       <b-input v-model="email" placeholder="Your Email" autofocus></b-input>
                   </b-field>
               </div>
               <footer class="modal-card-foot">
                   <button class="button is-success" @click="sendResetLink">Reset Password</button>
               </footer>
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
                        this.$root.loading = false;
                        this.$root.showNotification('Please check your inbox. If there is an account for the email you provided, you will receive an email with instructions on how to reset your password.','success');
                        this.email=null;
                        this.$router.push({name:'home'})
                    })
                    .catch((error) => {
                        if (error.response.status===422)
                        {
                            if(error.response.data.message && !error.response.data.errors)
                            {
                                this.$root.showNotification('LOGIN FAILED. We could not find a user with that email and password. Please try again.','warning');
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
                    });
            },
            },
        activated()
        {
            this.$root.showLoginModal = false;
        }
    }
</script>