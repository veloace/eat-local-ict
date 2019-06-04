<template>
    <div class="has-background-translucent top-spacer">
        <div class="container main-container has-text-white" style="margin-top: 30px;">
            <h1 class="title has-text-white">Your Account</h1>
            <div class="columns is-multiline">
                <div class="column is-half">
                    <div class="card">
                        <div class="card-header has-background-black-ter">
                            <h1 class="card-header-title has-text-white is-size-4">Reset Your Password?</h1>
                        </div>
                        <div class="card-content has-background-grey-darker">
                            <b-field label="Current Password" custom-class="has-text-white" :type="errors.old_password ?'is-danger':''" :message="errors.old_password">
                                <b-input v-model="old_password"  type="password"  placeholder="Current Password" autofocus></b-input>
                            </b-field>

                            <b-field label=" New Password" custom-class="has-text-white" :type="errors.password ?'is-danger':''" :message="errors.password">
                                <b-input v-model="password" type="password" placeholder="Password" autofocus></b-input>
                            </b-field>

                            <b-field label="Re-Enter New Password" custom-class="has-text-white" :type="errors.password_confirmation ?'is-danger':''" :message="errors.password_confirmation">
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
                <div class="column is-half">
                    <div class="card">
                        <div class="card-header has-background-black-ter">
                            <h1 class="card-header-title has-text-white is-size-4">Account Settings</h1>

                        </div>
                        <div class="card-content has-background-grey-darker">
                            <b-field label="Your Name" custom-class="has-text-white" :type="errors.name ?'is-danger':''" :message="errors.name">
                                <b-input v-model="name"  type="text"  placeholder="Current Password" autofocus></b-input>
                            </b-field>
                            <b-field label="You Email" custom-class="has-text-white" :type="errors.email ?'is-danger':''" :message="errors.email">
                                <b-input v-model="email"  type="email"  placeholder="Current Password" autofocus></b-input>
                            </b-field>
                            <b-switch class="has-text-white" size="is-small" :value="false" :true-value="1" :false-value="0" v-model="newsletter">
                                <span v-if="newsletter===1">You are opted in to receive our periodic newsletter!</span>
                                <span v-else>You are NOT opted in to receive our newsletter... :(</span>
                            </b-switch>
                        </div>
                        <footer class="card-footer has-background-black-ter">
                            <p class="card-footer-item">
                                <button class="button is-success" @click="updateUser">Update Account</button>
                            </p>
                        </footer>
                    </div>
                </div>
                <div class="column is-half" v-if="places.length > 0">
                    <div class="card">
                        <div class="card-header has-background-black-ter">
                            <h1 class="card-header-title has-text-white is-size-4">Your Restaurants</h1>
                        </div>
                        <div class="card-content has-background-grey-darker has-text-white">
                            <p>Below are a list of the restaurants that you own and have claimed ownership of in EatLocalICT</p>
                            <ul >
                                <li v-for="place in places" >
                                    <p style="padding-bottom: 0;margin-bottom: 0">{{place.name}} <router-link :to="{name:'EditListing',params:{id:place.id}}"  v-if="place.claim_status==='approved' || place.claim_status==='pending'" >Edit Listing</router-link>
                                    <p class="heading">
                                        <span v-if="place.claim_status==='approved'" class="has-text-white"></span>
                                        <span v-else-if="place.claim_status==='denied'" class="has-text-danger">Your ownership claim has been denied.</span>
                                        <span v-else-if="place.claim_status==='pending'" class="has-text-info">Your ownership claim is pending approval by an EatLocalICT Admin.</span>
                                   </p>

                                </li>
                            </ul>
                        </div>


                    </div>

                </div>

                <div class="column is-half">
                    <div class="card">
                        <div class="card-header has-background-black-ter">
                            <h1 class="card-header-title has-text-white is-size-4">Delete Your Account</h1>
                        </div>
                        <div class="card-content has-background-grey-darker has-text-white">
                            <p>You may delete your account at any time, but we must warn you that we take your privacy seriously.<span class="has-text-danger"> When you delete your account, we will immediately purge all of your data from our database.</span></p>
                            <p>This means:</p>
                            <ul>
                                <li>Your name, email, and all other information is gone, so we will never be able to recover your account.</li>
                                <li>Any saved preferences, lists, favorites, etc. will be deleted and unrecoverable.</li>
                                <li>Any restaurants that you managed will still exist, but they will be open for someone else to claim.</li>
                            </ul>
                            <p>After you delete this account, you can create a new account at any time with the same email or name, but it will be as if you created an entirely new account.</p>
                        </div>
                        <footer class="card-footer has-background-black-ter">
                            <p class="card-footer-item">
                                <button class="button is-danger" @click="showDeleteModal=true">Delete Account</button>
                            </p>
                        </footer>
                    </div>

                </div>


            </div>

        </div>
        <b-modal :active.sync="showDeleteModal" :width="640" scroll="keep">
            <div class="modal-card">
                <header class="modal-card-head has-background-danger">
                    <p class="modal-card-title">Delete Account?</p>
                </header>
                <div class="modal-card-body  has-background-grey-darker has-text-white">
                    <p>Are you sure you want to delete you account?</p>
                    <p class="has-text-danger">THIS ACTION CANNOT BE UNDONE. ALL YOU DATA ON EAT LOCAL ICT WILL BE LOST.</p>
                    <p>If you still want to delete your account, please enter your password and click 'Delete Account' below.</p>

                    <b-field label="Password" custom-class="has-text-white" :type="errors.delete_password ?'is-danger':''" :message="errors.delete_password">
                        <b-input v-model="delete_password" type="password" placeholder="Password" autofocus></b-input>
                    </b-field>
                </div>
                <footer class="modal-card-foot">
                    <button  @click="showDeleteModal=false" class="button is-success" type="button" >Cancel (Keep Account)</button>
                    <button class="button is-danger" @click="deleteAccount">Delete Account</button>
                </footer>
            </div>
        </b-modal>
    </div>
</template>
<script>
export default {
    data() {
        return {
            old_password:null,
            password:null,
            delete_password:null,
            password_confirmation:null,
            name:'',
            email:'',
            newsletter:0,
            errors:{
                old_password:null,
                password:null,
                password_confirmation:null,
                delete_password:null,
                email:null,
                name:null
            },
            showDeleteModal:false,
            places:[]
        }
    },
    methods:{
        resetPassword()
        {
            this.$root.loading= true;
            this.resetErrors();
            axios.post('api/user/password',{
                old_password:this.old_password,
                password:this.password,
                password_confirmation:this.password_confirmation,
            })
                .then((response)=>{
                    this.$root.loading= false;
                    this.$root.showNotification('Your password has been changed','success');
                    this.old_password=null;
                    this.password=null;
                    this.password_confirmation=null;
                })
                .catch((error) => {
                    this.$root.loading= false;
                    if (error.response.status===422)
                    {
                        if(error.response.data.message && !error.response.data.errors)
                        {
                            this.$root.showNotification('We couldn\'t reset your password. Please check the form and try again..','warning');
                            this.password=null;
                        }
                        else
                        {
                            this.$root.showNotification('We couldn\'t reset your password. Please check the form for errors and try again','warning');

                            let errors = error.response.data.errors;
                            for (const [key, value] of Object.entries(errors)) {
                                this.errors[key] = value[0];
                            }
                        }

                    }
                })
        },
        deleteAccount()
        {
            this.$root.loading= true;
            this.resetErrors();
            axios.post('api/user/deleteAccount',{
                delete_password:this.delete_password,
            })
            .then((response)=>{
                this.$root.loading= false;
                this.$root.showNotification('Your account has been deleted. Please note that you can continue to use certain functions of EatLocalICT even without an account.','success');
                this.delete_password=null;
                this.showDeleteModal = false;
                this.$root.isAuthenticated(false,true);//make sure user is logged out on front end
                this.$router.push({name:'home'})
            })
                .catch((error) => {
                    this.$root.loading= false;
                    if (error.response.status===422)
                    {
                        if(error.response.data.message && !error.response.data.errors)
                        {
                            this.$root.showNotification('We couldn\'t delete your account. Please try again or contact customer support.','warning');
                            this.password=null;
                        }
                        else
                        {
                            this.$root.showNotification('We couldn\'t delete your account. Please make sure your provided the correct password','warning');

                            let errors = error.response.data.errors;
                            for (const [key, value] of Object.entries(errors)) {
                                this.errors[key] = value[0];
                            }
                        }

                    }
                    else
                    {
                        this.$root.showNotification('We couldn\'t delete your account. Please try again or contact customer support.','warning');
                        this.password=null;
                    }
                })

        },
        getOwnedRestaurants()
        {
            axios.get('/api/places/owner')
                .then((response)=>{

                    this.places = response.data;
                })
                .catch(()=>{
                    this.$root.showNotification('We encountered an error and couldn\'t load your restaurants.','warning');

                })
        },
        getUserInfo()
        {
            axios.get('/api/user')
                .then((response)=>{

                    this.name = response.data.name;
                    this.email = response.data.email;
                    this.newsletter = response.data.can_get_newsletter;
                })
                .catch(()=>{
                    this.$root.showNotification('We encountered an error and couldn\'t load your restaurants.','warning');

                })
        },
        updateUser()
        {
            this.resetErrors();
            this.$root.loading= true;
            axios.post('/api/user',{
                name:this.name,
                email:this.email,
                newsletter:this.newsletter,
                _method:'PATCH'
            })
                .then((response)=>{
                    if(response.status===204)
                    {
                        this.$root.showNotification('You account has been updated!','success');
                    }
                    else if (response.data.message)
                    {
                        this.$root.showNotification(response.data.message,'success');

                    }
                    this.$root.loading= false;

                })
                .catch((error)=>{

                    if (error.response.status===422)
                    {
                        let errors = error.response.data.errors;
                        for (const [key, value] of Object.entries(errors)) {
                            this.errors[key] = value[0];
                            console.log(key,value);

                        }
                        this.$root.showNotification('Looks like there was an error in the data you provided. Please check the form for errors.','warning');

                    }
                    else
                    {
                        this.$root.showNotification('We encountered an error and couldn\'t update your account.','warning');

                    }
                    this.$root.loading= false;

                })
        },
        resetErrors()
        {
            this.errors={
                old_password:null,
                password:null,
                password_confirmation:null,
                delete_password:null,
                email:null,
                name:null
            };
        }
    },
    activated()
    {
        this.$root.isAuthenticated(false,true);//make sure user is logged in on front end
        this.getOwnedRestaurants();
        this.getUserInfo();
    }
}
</script>
<style>
    .main-container
    {
        min-height:100vh;
    }
    ul
    {
        list-style-type: circle;
        padding-left: 15px;
    }
</style>