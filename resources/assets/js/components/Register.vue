<template>
    <b-modal :active.sync="$root.showRegistrationModal" v-if="!$root.user.logged" :width="640" :canCanel="['x', 'outside']">
        <div class="modal-background"></div>
        <div class="modal-card has-background-grey-darker">
            <header class="modal-card-head">
                <p class="modal-card-title">Register</p>
            </header>
            <div class="modal-card-body">
                <p class="subtitle has-text-white">You don't need an account to access EatLocalICT, but you can create a <strong class="has-text-white">FREE</strong> account to save favorites, make lists, and to claim ownership of restaurants!</p>
                    <b-field  label="Name" custom-class="has-text-white" :type="errors.name ?'is-danger':''" :message="errors.name"  >
                        <b-input   minlength="2" maxlength="100" v-model="name" placeholder="Your Name" autofocus></b-input>
                    </b-field>

                    <b-field  label="Email" custom-class="has-text-white" :type="errors.email ?'is-danger':''" :message="errors.email" >
                        <b-input minlength="2" maxlength="150" v-model="email" placeholder="Your Email" autofocus></b-input>
                    </b-field>

                    <b-field  label="Password" custom-class="has-text-white" :type="errors.password ?'is-danger':''" :message="errors.password" >
                        <b-input   v-model="password" type="password" placeholder="Your Password" required></b-input>
                    </b-field>

                    <b-field  label="Re-type Password" custom-class="has-text-white" :type="errors.password_confirmed ?'is-danger':''" :message="errors.password_confirmed" >
                        <b-input v-model="password_confirmed" type="password" placeholder="Confirm Password" required></b-input>
                    </b-field>
                    <p class="has-text-white">By clicking the sign-up button below, you agree to our <a @click="$root.toggleLegalInfoModal('terms')" >terms and conditions</a> and our <a @click="$root.toggleLegalInfoModal('cookies')" >cookie policy</a>.</p>
            </div>
            <footer class="modal-card-foot">
                <button class="button" @click="$root.showLoginModal=true;$root.showRegistrationModal = false" >Back to Login</button>
                <invisible-recaptcha :sitekey="$root.recaptcha" class="button is-success" :callback="register" >Sign Up!</invisible-recaptcha >
            </footer>
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
            register(recaptchaToken){
                this.errors={
                    name:null,
                        email:null,
                        password:null,
                        password_confirmed:null
                };
                this.$root.isLoading = true;
                console.log(recaptchaToken);
                axios.post('/api/user/register',{
                    email:this.email,
                    password:this.password,
                    password_confirmation:this.password_confirmed,
                    name:this.name,
                    'g-recaptcha-response':recaptchaToken
                })
                    .then((response) => {
                        this.$root.isLoading = false;
                        console.log(response);
                        this.$root.user.name = this.name;
                        this.$root.user.logged = true;
                        let registerMessage = 'Hi, '+this.name +'! Welcome to EatLocalICT!';
                        this.$root.showNotification(registerMessage,'success');
                        this.showLoginModal=false;
                        this.$root.showRegistrationModal = false;

                    })
                    .catch((error) => {
                        if (error.response.status===422)
                        {
                            this.$root.isLoading = false;

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