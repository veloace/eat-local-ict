
<template>
   <div>
      <!-- Hero content: will be in the middle -->
      <div class="hero is-fullheight">
         <div class="hero-body">
            <div class="container has-text-centered">
               <h1 class="title has-text-white">
                  EAT & DRINK LOCAL IN WICHITA
               </h1>
               <h2 class="subtitle has-text-white">
                  Why eat at a chain if you can eat local?
               </h2>

               <p>
                  <button v-on:click="findRandomPlace" class="button is-link is-inverted  is-outlined is-large has-text-weight-bold	">Random Place</button>
               </p>
               <p>
                  <router-link :to="{name:'search'}"  class="has-text-white has-text-weight-bold">Advanced Search</router-link>
               </p>
               <!--<p class="has-text-white">Have a suggestion or data correction?<a @click="showFeedBackModal">Let us Know</a> </p>-->

            </div>
         </div>
         <!-- Hero footer: will stick at the bottom -->
         <div class="hero-foot">
            <nav class="tabs">
               <div class="container">
                  <ul>
                     <li><router-link  class="has-text-white" :to="{name:'backer'}">Help Us Out!</router-link></li>
                     <li><a  class="has-text-white" href="mailto:suggestion@eatlocalict.com?subject=Suggestion%20for%20EatLocalICT">Suggest an Edit</a></li>
                  </ul>
               </div>
            </nav>
         </div>
      </div>

      <div class="modal" v-bind:class="{ 'is-active': showPlaceModal }">
         <div class="modal-background"></div>
            <!-- Any other Bulma elements you want -->

            <div class="modal-card">
               <header class="modal-card-head">
                  <p class="modal-card-title">How About...<i v-if="loadingRandom" class="fa fa-spinner fa-spin"></i></p>
               </header>

                  <section class="modal-card-body has-text-white">
                     <h1 class="subtitle has-text-white">{{randomPlace.name}}
                        <sup>
                           <span class="is-size-7 has-text-success" v-if="randomPlace.is_open">(Open Now!)</span>
                           <span class="is-size-7 has-text-danger" v-else>(Closed Now)</span>
                        </sup>
                     </h1>
                     <p class="has-text-white" style="padding-bottom: 10px;">{{randomPlace.summary}}</p>
                     <p class="heading has-text-white">Located at: {{randomPlace.address}} {{randomPlace.city}}, {{randomPlace.state_code}}</p>
                     <p>
                        <label class="checkbox">
                           <input type="checkbox" v-model="filters.is_open" :true-value="1" :false-value="0">
                           Only show open places.
                        </label>
                     </p>
                  </section>
                  <footer class="modal-card-foot">
                     <router-link :disabled="loadingRandom" :to="{name:'listing',params:{id:randomPlace.id},query:{ref:'home'}}" class="button is-small is-success">Sounds Good!</router-link>
                     <button :disabled="loadingRandom" class="button is-small is-primary" v-on:click="findRandomPlace">Try Something Else</button>
                     <button class="button is-danger is-small" v-on:click="showPlaceModal=false">Cancel</button>
                     <hr>
                  </footer>


            </div>
         <button class="modal-close is-large" aria-label="close" v-on:click="showPlaceModal=false"></button>
      </div>
   </div>
</template>
<script>
    export default {
        data(){
            return{
                loading:false,
                items:window.tags,
                selectedTag:null,
                loadingRandom:false,
                randomPlace:{
                    id:0,
                    name:null,
                    summary:null,
                    address:null,
                    city:null,
                    state_code:null,
                    is_open:null
                },
                filters:{
                  is_open:1
                },
                showPlaceModal:false
            }
        },
        methods:{
           findRandomPlace()
            {
                this.loadingRandom = true;
                this.showPlaceModal=true;
                axios.get('/api/places/random',{params:this.filters})
                    .then((response)=> {
                      this.loadingRandom = false;
                        this.randomPlace = response.data;
                    })
                    .catch((error)=> {
                       this.loadingRandom = false;
                       this.showPlaceModal=false;
                    })
            },//findRandomPlace
            goToSearch()
            {
                this.$router.push({ name: 'search', params: { term: this.selectedTag }})

            }

        },
        showFeedBackModal()
        {


        },
        activated(){
            //clear out last state
            this.showPlaceModal=false;
        }
    }
</script>