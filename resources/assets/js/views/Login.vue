<template>
    <div class="hero is-fullheight ict-flag-bg">
        <div class="hero-body">
            <div class="container">
                <div class="columns">
                    <div class="column has-background-grey-darker is-offset-one-quarter is-half">
                        <h1 class="has-text-centered has-text-white title">Login to EatLocalICT</h1>

                        <p class="subtitle has-text-white">Please login to access your lists, favorites, and more!</p>
                        <b-field label="Email" custom-class="has-text-white" :type="errors.email ?'is-danger':''" :message="errors.email">
                            <b-input v-model="email" placeholder="Your Email" ></b-input>
                        </b-field>

                        <b-field label="Password" custom-class="has-text-white" :type="errors.password ?'is-danger':''" :message="errors.password">
                            <b-input @keyup.native.enter="login" v-model="password" password-reveal type="password" placeholder="Your Password"></b-input>
                        </b-field>
                        <p class="has-text-white">
                            <b-switch  size="is-small" :value="false" true-value="1" false-value="0" v-model="remember">
                                Remember Me
                            </b-switch>

                        </p>

                        <p class="has-text-white"> Don't have an account yet?
                            <router-link  :to="{name:'register'}">Sign Up Now</router-link>
                        </p>
                        <router-link :to="{name:'passwordResetRequest'}" class="button" type="button" >Forgot Password</router-link>
                        <button class="button is-success" @click="login">Login</button>
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
                'email':null,
                'password':null,
                'remember':0,
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
                        this.email=null;
                        this.password=null;
                        this.$router.push({name:'account'});
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
                        else
                        {
                            this.password=null;
                            this.$root.showNotification('LOGIN FAILED. Please refresh the page and try again.','warning');

                        }
                    });
            }
        },
        activated()
        {
            this.$root.isAuthenticated(true,false,'home')
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