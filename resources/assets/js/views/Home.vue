
<template>
   <div>
      <!-- Hero content: will be in the middle -->
      <div class="hero is-fullheight ict-flag-bg">
         <div class="hero-body">
            <div class="container has-text-centered top-spacer">
               <h1 class="title has-text-white">EatLocalICT</h1>

               <h2 class="subtitle has-text-white">
                  Find locally-owned establishments that are unique to Wichita and the surrounding area.
               </h2>

               <p>
                  <button @click="findRandomPlace" class="button is-link is-inverted  is-outlined is-large has-text-weight-bold	">Random Place</button>
               </p>
               <p>
                  <router-link :to="{name:'search'}"  class="button is-link   is-outlined has-text-weight-bold">Advanced Search</router-link>
               </p>
               <hr>
               <p class="has-text-white">Help us add features and keep our servers running!</p>
               <a href='https://ko-fi.com/T6T2KAM0' target='_blank'><img height='36' style='border:0px;height:36px;' src='https://az743702.vo.msecnd.net/cdn/kofi1.png?v=2' border='0' alt='Buy Me a Coffee at ko-fi.com' /></a>
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

      <b-modal :active.sync="showPlaceModal" :width="640">
         <div class="modal-background"></div>
            <div class="modal-card">
               <header class="modal-card-head">
                  <p class="modal-card-title">How About...<i v-if="loadingRandom" class="fa fa-spinner fa-spin"></i></p>
               </header>

               <section  v-if="loadingRandom" class="modal-card-body has-text-white">
                  <h1 >Randomizing...Please Wait.</h1>

               </section>
                  <section v-else class="modal-card-body has-text-white">
                     <h1 class="subtitle has-text-white random-place-card-title"><router-link :to="{name:'listing',params:{id:randomPlace.id},query:{ref:'home'}}">{{randomPlace.name}}</router-link>
                        <sup>
                           <span class="is-size-7 has-text-success" v-if="randomPlace.is_open">(Open Now!)</span>
                           <span class="is-size-7 has-text-danger" v-else>(Closed Now)</span>
                        </sup>
                     </h1>
                     <p class="heading has-text-white"><i class="fa fa-map-marker"></i>&nbsp;Located at {{randomPlace.address}} in {{randomPlace.city}}, {{randomPlace.state_code}}
                        <span v-if="randomPlace.user_distance" class="has-text-primary">
                           (About {{randomPlace.user_distance}} miles away)
                        </span>
                     </p>
                      <p v-if="randomPlace.is_favorited" class="heading has-text-warning" style="padding-top: 0"><i class="fa fa-star has-text-warning"></i> In your favorites.</p>
                     <p v-if="randomPlace.summary" class="has-text-white is-italic" style="padding-bottom: 10px;">{{randomPlace.summary}}</p>
                     <div v-else class="has-text-white" style="padding-bottom: 10px;">
                        <p class="is-italic"><em>We don't have a description for this place yet.</em></p>
                        <a class="button is-info is-outlined is-small" @click="suggestDescription">Suggest a Description</a>
                     </div>
                     <p>
                      <!--  <label class="heading">
                           <b-switch  v-model="filters.is_open" :true-value="1" :false-value="0">
                              {{filters.is_open ? 'Only showing places that are open right now.':'Showing all places. Check to only show open places.'}}
                           </b-switch>
                        </label>-->

                     </p>
                     <b-field label="Distance" class="has-text-white" v-if="$root.geo.lat && $root.geo.lng">

                        <b-select  placeholder="Distance" v-model="filters.distance">
                           <option
                                   v-for="option in distances"
                                   :value="option.id"
                                   :key="option.id">
                              {{option.text }}
                           </option>
                        </b-select>
                     </b-field>

                      <router-link :disabled="loadingRandom" v-on:click="showPlaceModal=false" :to="{name:'listing',params:{id:randomPlace.id},query:{ref:'home'}}" class="button is-success  is-outlined">See Full Listing</router-link>

                  </section>
                  <footer class="modal-card-foot buttons">
                     <button :disabled="loadingRandom" class="button is-small is-primary is-inverted is-outlined" v-on:click="findRandomPlace">Roll Again</button>
                     <add-to-list :place="randomPlace" listType="SaveForLater" color="is-warning"></add-to-list>
                     <button class="button is-small is-danger is-inverted is-outlined" v-on:click="showPlaceModal=false">Cancel</button>
                     <hr>
                  </footer>


            </div>
         <button class="modal-close is-large" aria-label="close" v-on:click="showPlaceModal=false"></button>
      </b-modal>
   </div>
</template>
<script>
    import AddToList from "../components/AddToList";
    export default {
        components: {AddToList},
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
                    is_open:null,
                    user_distance:null
                },
                filters:{
                  is_open:1,
                 lat:this.$root.geo.lat,
                 lng:this.$root.geo.lng,
                    distance:4
                },
                showPlaceModal:false,
                distances:[
                    {
                        id:0,
                        text:'All Locations'
                    },
                    {
                        id:1,
                        text:'Under a mile'
                    },
                    {
                        id:2,
                        text:'Under 3 miles'
                    },
                    {
                        id:3,
                        text:'Under 5 miles'
                    },
                    {
                        id:4,
                        text:'Under 10 miles'
                    },
                    {
                        id:5,
                        text:'Under 20 miles'
                    },

                ]
            }
        },
        methods:{
            /**
             * makes API call to get a random place
             */
           findRandomPlace()
            {
                this.filters.lat=this.$root.geo.lat;
                this.filters.lng=this.$root.geo.lng;
                this.loadingRandom = true;
                this.showPlaceModal=true;
                let params = {params:this.filters};
                axios.get('/api/places/random',params)
                    .then((response)=> {
                      this.loadingRandom = false;
                        this.randomPlace = response.data;
                    })
                    .catch((error)=> {
                       this.loadingRandom = false;
                       this.showPlaceModal=false;

                       this.$root.showNotification('We encountered an error and were unable to load a random place. If this problem persists, try refreshing the page.','danger');
                    });


            },//findRandomPlace
            suggestDescription()
            {
                this.$root.showDescriptionSuggestionModal(this.randomPlace.id,this.randomPlace.name);
            }

        }
    }
</script>
<style>
   .random-place-card-title
   {
      margin-bottom: 0!important;
   }
   .has-text-white > label{
      color: white;
   }
</style>