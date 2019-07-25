<template>
    <div class="hero is-fullheight ict-flag-bg">
        <div class="hero-body">
            <div class="container">
                <div class="columns">
                    <div class="column has-background-grey-darker is-offset-2 is-8">
                        <h1 class="has-text-centered has-text-white title">Signup For EatLocalICT</h1>

                        <p class="subtitle has-text-white">You don't need an account to access EatLocalICT, but you can create a <strong class="has-text-white">FREE</strong> account to save favorites, make lists, and to claim ownership of restaurants!</p>
                    <div class="columns">
                        <div class="column">
                            <b-field  label="Name" custom-class="has-text-white" :type="errors.name ?'is-danger':''" :message="errors.name"  >
                                <b-input   minlength="2" maxlength="100" v-model="name" placeholder="Your Name" ></b-input>
                            </b-field>
                        </div>
                        <div class="column">
                            <b-field  label="Email" custom-class="has-text-white" :type="errors.email ?'is-danger':''" :message="errors.email" >
                                <b-input minlength="2" maxlength="150" v-model="email" placeholder="Your Email" ></b-input>
                            </b-field>
                        </div>
                    </div>

                    <div class="columns">
                        <div class="column">
                            <b-field  label="Password" custom-class="has-text-white" :type="errors.password ?'is-danger':''" :message="errors.password" >
                                <b-input   v-model="password" type="password" placeholder="Your Password" required></b-input>
                            </b-field>
                        </div>
                        <div class="column">
                            <b-field  label="Re-type Password" custom-class="has-text-white" :type="errors.password_confirmed ?'is-danger':''" :message="errors.password_confirmed" >
                                <b-input v-model="password_confirmed" type="password" placeholder="Confirm Password" required></b-input>
                            </b-field>
                        </div>
                    </div>

                    <p class="has-text-white heading">In the future, we are considering adding a periodic newsletter of updates to the app and information about new and updated restaurants. Would you like to opt-in to receiving this newsletter if we decide to do it? (You can opt out at any time)</p>
                    <b-switch class="has-text-white" size="is-small" :value="false" :true-value="1" :false-value="0" v-model="newsletter">
                        <span v-if="newsletter===1">You are opted in!</span>
                        <span v-else>You are NOT opted in</span>
                    </b-switch>
                    <p class="has-text-white">By clicking the sign-up button below, you agree to our <a @click="$root.toggleLegalInfoModal('terms')" >terms and conditions</a> and our <a @click="$root.toggleLegalInfoModal('cookies')" >cookie policy</a>.</p>
                <router-link :to="{name:'login'}" class="button"  >Back to Login</router-link>
                <button class="button is-success" @click="register">Sign Up!</button>
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
                name:null,
                email:null,
                password:null,
                password_confirmed:null,
                errors:{
                    name:null,
                    email:null,
                    password:null,
                    password_confirmed:null
                },
                showLoginModal:true,
                newsletter:1
            }
        },
        methods:{
            register(recaptchaToken=null){
                this.errors={
                    name:null,
                        email:null,
                        password:null,
                        password_confirmed:null
                };
                this.$root.loading = true;
                axios.post('/api/user/register',{
                    email:this.email,
                    password:this.password,
                    password_confirmation:this.password_confirmed,
                    name:this.name,
                    //'g-recaptcha-response':recaptchaToken,
                    newsletter:this.newsletter
                })
                    .then((response) => {
                        this.$root.loading = false;
                        this.$root.user.name = this.name;
                        this.$root.user.logged = true;
                        let registerMessage = 'Hi, '+this.name +'! Welcome to EatLocalICT!';
                        this.$root.showNotification(registerMessage,'success');
                        this.$router.push({name:'account'})


                    })
                    .catch((error) => {
                        this.$root.loading = false;
                        if (error.response.status===422)
                        {
                            this.$root.loading = false;

                            let errors = error.response.data.errors;
                            for (const [key, value] of Object.entries(errors)) {
                                this.errors[key] = value[0];
                            }
                            this.$root.showNotification('We found errors with your registration. Please check the form and try again.','danger')
                        }
                        else
                        {
                            this.$root.showNotification('We encountered an error while trying to process your registration. Please try again, or refresh the page if the problem persists.','danger')

                        }
                    });
            }
        },
        activated()
        {
            this.$root.isAuthenticated(true,false,'account')
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