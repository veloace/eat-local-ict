<template>
    <b-modal :active.sync="$root.showLoginModal" v-if="!$root.user.logged" :width="640" :canCanel="['x', 'outside']">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Login</p>
            </header>
            <div class="modal-card-body  has-background-grey-darker">
                <p class="subtitle has-text-white">Please login to access your lists, favorites, and more!</p>
                        <b-field label="Email" custom-class="has-text-white" :type="errors.email ?'is-danger':''" :message="errors.email">
                            <b-input v-model="email" placeholder="Your Email" autofocus></b-input>
                        </b-field>

                        <b-field label="Password" custom-class="has-text-white" :type="errors.password ?'is-danger':''" :message="errors.password">
                            <b-input  v-model="password" password-reveal type="password" placeholder="Your Password"></b-input>
                        </b-field>
                    <p class="has-text-white">
                        <b-switch  size="is-small" :value="false" true-value="1" false-value="0" v-model="remember">
                            Remember Me
                        </b-switch>

                    </p>

                    <p class="has-text-white"> Don't have an account yet?
                        <a  @click="register">Sign Up Now</a>
                    </p>
            </div>
            <footer class="modal-card-foot">
                <router-link :to="{name:'passwordResetRequest'}" @click="$root.showLoginModal=false" class="button" type="button" >Forgot Password</router-link>
                <button class="button is-success" @click="login">Login</button>
            </footer>
        </div>
    </b-modal>
</template>
<script>
    export default {
        data() {
            return {
                'email':null,
                'password':null,
                'remember':0,
                showLoginModal:true,
                errors:{
                    email:null,
                    password:null
                },
            }
        },
        methods:{
            login(){
                this.$root.loading = true;
                this.errors={
                    email:null,
                    password:null
                };
                axios.post('/api/user/login',{
                    email:this.email,
                    password:this.password,
                    remember:this.remember
                })
                    .then((response) => {
                        this.$root.loading = false;
                        console.log(response);
                        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = response.data.token;
                        this.$root.user.name = response.data.user.name;
                        this.$root.user.logged = true;
                        let loginMessage = 'Hi, '+this.$root.user.name +'! Welcome back to EatLocalICT!';
                        this.$root.showNotification(loginMessage,'success');
                        this.showLoginModal=false;
                        this.$root.showLoginModal=false;
                        this.email=null;
                        this.password=null;
                    })
                    .catch((error) => {
                        this.$root.loading = false;
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
            register(){
                this.$root.showRegistrationModal = true;
                this.$root.showLoginModal= false;

            }
        }
    }
</script>
<style>
    .box {
        margin-top: 5rem;
    }
    input {
        font-weight: 300;
    }
    p {
        font-weight: 700;
    }
    p.subtitle {
        padding-top: 1rem;
    }
    . button-login
    {
        margin-top: 25px;
    }
</style>