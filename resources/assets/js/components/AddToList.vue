<template>
    <span v-if="place.id!==null && place.id!==0 && !suppress" class="kinda-button">
        <button class="button is-inverted is-outlined" :class="[buttonSize,color]"  v-if="listType==='SaveForLater'" @click="addToSavedForLater" :disabled="loading">Save For Later</button>
        <button class="button is-inverted is-outlined" :class="[buttonSize,color]"   v-else @click="addToFavorites" :disabled="loading">Add To Favorites</button>


       <b-modal :active.sync="showLoginPrompt" :width="640">
        <div class="modal-background"></div>
        <div class="modal-card" >
            <header class="modal-card-head">
                <h2 class="modal-card-title">Login Required</h2>
            </header>
            <section class="modal-card-body has-text-white">
                <p>You don't need an account for EatLocalICT, but you do need an account to save a place for later or to add a place to your favorites.</p>
                <p>You can choose to <span class="has-text-success" @click="login">login</span> or <span class="has-text-success" @click="register">create a FREE account</span> now, or you can hit <span class="has-text-danger" @click="cancel">cancel</span> to keep using EatLocalICT without an account.</p>
            </section>
            <footer class="modal-card-foot">
                <button class="button is-small is-success" @click="login">Login</button>
                <button class="button is-small is-success" @click="register">Create FREE Account</button>
                <button class="button is-danger is-small" @click="cancel">Cancel</button>
            </footer>
        </div>
    </b-modal>

   <b-modal :active.sync="showCommentModal" :width="640">
        <div class="modal-background"></div>
        <div class="modal-card" >
            <header class="modal-card-head">
                <h2 class="modal-card-title">Add a Note?</h2>
            </header>
            <section class="modal-card-body has-text-white">
                <p>Would you like to add a note to yourself as to why you love this place? (this is optional)</p>
               <b-field label="Notes" custom-class="has-text-white">
                <b-input v-model="comment" maxlength="175" type="textarea" placeholder="Your comments, e.g 'This place had really good wings and a great wait staff'."></b-input>
            </b-field>
            </section>
            <footer class="modal-card-foot">
                <button class="button is-small is-success" @click="finishAddToFavorites">Continue</button>
            </footer>
        </div>
    </b-modal>

    </span>
</template>
<script>
    export default{
        props: {
            'listType':{
                listType: String,
                default: 'Favorites'
            },
            buttonSize:{
                listType: String,
                default: 'is-small'
            },
            'place':null,
            'color':{
                listType: String,
                default: 'is-primary'
            },
        },
        data(){
            return {
                showLoginPrompt:false,
                showCommentModal:false,
                loading:false,
                suppress:false,
                comment:null
            };
        },
        methods: {

            addToFavorites()
            {
                if(this.checkForLogin())
                {
                    this.showCommentModal = true;
                }
            },

            finishAddToFavorites()
            {
                this.loading = true;

                this.makeAPICall('favorites')


            },
            addToSavedForLater()
            {
                if(this.checkForLogin())
                {
                    this.loading = true;
                    this.makeAPICall('later')
                }
            },

            checkForLogin()
            {
                if(!this.$root.user.logged)
                {
                    this.showLoginPrompt=true;
                    return false;
                }
                return true;
            },
            login(){
                this.$root.showLoginModal=true;
                this.showLoginPrompt = false;

            },
            register()
            {
                this.$root.showRegistrationModal=true;
                this.showLoginPrompt = false;
            },
            cancel()
            {
                this.showLoginPrompt = false;
            },

            makeAPICall(type)
            {
                let url=null;
                let success_message=null;

                if(type==='favorites')
                {
                     url='/api/places/lists/favorites';
                     success_message = "Added "+this.place.name+" to your favorites.";
                     this.$emit('add-success');
                }
                else
                {
                     url='/api/places/lists/savedForLater';
                     success_message = "Saved "+this.place.name+" for later.";
                }

                axios.post(url,{place:this.place.id,comment:this.comment})
                    .then((response)=>{
                        this.$root.showNotification(success_message,'info');
                        this.loading=false;
                        this.suppress=true;
                    })
                    .catch((error)=>{
                        this.$root.showNotification('We encountered an error while trying to add this restaurant to you list.','danger');
                        this.loading=false;
                    })
            }

        },
        watch: {
            place: function() { // watch it
                this.suppress=false;
            }
        }

    }
</script>
<style>
    .buttons span:not(:last-child):not(.is-fullwidth)
    {
        margin-right: 0.5rem;
    }
    .has-text-white > label{
        color: white;
    }

    .button.is-warning.is-inverted.is-outlined
    {
        border-color: white;
        color:white;
    }
    .button.is-warning.is-inverted.is-outlined:hover, .button.is-warning.is-inverted.is-outlined:focus
    {
        background-color:#fff;
    }
</style>