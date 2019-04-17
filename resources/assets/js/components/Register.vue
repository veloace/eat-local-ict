<template>
    <b-modal :active.sync="$root.showRegistrationModal" v-if="!$root.user.logged">
        <div class="modal-background"></div>
        <div class="modal-card">
            <div class="modal-card-body">
                <h3 class="title has-text-white">Register</h3>
                <p class="subtitle has-text-white">You don't need an account to access EatLocalICT, but you can create a <strong class="has-text-white">FREE</strong> account to save favorites, make lists, and claim restaurants!</p>
                <div class="box">
                    <b-field :type="errors.name ?'is-danger':''" :message="errors.name"  >
                        <b-input size="is-large"  minlength="2" maxlength="100" v-model="name" placeholder="Your Name" autofocus></b-input>
                    </b-field>

                    <b-field :type="errors.email ?'is-danger':''" :message="errors.email" >
                        <b-input size="is-large" minlength="2" maxlength="150" v-model="email" placeholder="Your Email" autofocus></b-input>
                    </b-field>

                    <b-field :type="errors.password ?'is-danger':''" :message="errors.password" >
                        <b-input size="is-large"  v-model="password" type="password" placeholder="Your Password" required></b-input>
                    </b-field>

                    <b-field :type="errors.password_confirmed ?'is-danger':''" :message="errors.password_confirmed" >
                        <b-input size="is-large" v-model="password_confirmed" type="password" placeholder="Confirm Password" required></b-input>
                    </b-field>
                    <div class="has-text-centered">
                        <p>By clicking the sign-up button below, you agree to our <a @click="$root.toggleLegalInfoModal('terms')" >terms and conditions</a> and our <a @click="$root.toggleLegalInfoModal('cookies')" >cookie policy</a>.</p>
                        <button class="button is-block is-success is-large is-fullwidth" @click="register">Sign Up!</button>
                    </div>

                </div>
                <p class="has-text-grey has-text-centered">
                    <a >Login</a> &nbsp;·&nbsp;
                    <a >Forgot Password</a>
                </p>
            </div>
        </div>
    </b-modal>
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
                showLoginModal:true
            }
        },
        methods:{
            register(){
                axios.post('/api/user/register',{
                    email:this.email,
                    password:this.password,
                    password_confirmation:this.password_confirmed,
                    name:this.name
                })
                    .then((response) => {
                        console.log(response);
                        this.$root.user.name = this.name;
                        this.$root.user.logged = true;
                        let registerMessage = 'Hi, '+this.name +'! Welcome to EatLocalICT!';
                        this.$root.showNotification(registerMessage,'success');
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