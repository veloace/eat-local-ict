<template>
    <b-modal :active.sync="$root.showLoginModal" v-if="!$root.user.logged">
        <div class="modal-background"></div>
        <div class="modal-card">
            <div class="modal-card-body has-text-centered">
                    <h3 class="title has-text-white">Login</h3>
                    <p class="subtitle has-text-white">Please login to access your lists, favorites, and more!</p>
                    <div class="box">
                            <b-field :type="errors.name ?'is-danger':''" :message="errors.name">
                                <b-input size="is-large" v-model="email" placeholder="Your Email" autofocus></b-input>
                            </b-field>

                            <b-field :type="errors.password ?'is-danger':''" :message="errors.password">
                                <b-input size="is-large" v-model="password" type="password" placeholder="Your Password"></b-input>
                            </b-field>

                            <b-switch  size="is-small" :value="false" true-value="1" false-value="0" v-model="remember">
                                Remember Me
                            </b-switch>

                            <button class="button is-block is-success is-large is-fullwidth" @click="login">Login</button>
                    </div>
                    <p class="has-text-grey">
                        <a  @click="register">Sign Up</a> &nbsp;·&nbsp;
                        <a  >Forgot Password</a>
                    </p>
            </div>
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

                    })
                    .catch((error) => {
                        if (error.response.status===422)
                        {
                            console.log('Validation Errors');
                            let errors = error.response.data.errors;
                            for (const [key, value] of Object.entries(errors)) {
                                this.errors[key] = value[0];
                            }
                            this.$root.showNotification('We found errors with your registration. Please check the form and try again.','danger')
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
</style>