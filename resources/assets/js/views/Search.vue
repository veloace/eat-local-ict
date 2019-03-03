<template>
    <div class="has-background-translucent top-spacer">


    <div class="container has-text-white has-background-translucent" style="margin-top: 30px;">

        <b-collapse class="panel panel-form-heading" :open.sync="filtersVisible">
            <div slot="trigger" class="panel-heading has-background-info ">
                <strong class="has-text-white">Advanced Search Filters</strong>
            </div>
            <div class="container has-background-white">
                <div class="columns">
                    <div class="column is-6">
                        <b-field >
                            <b-input  v-model="search.name" placeholder="Search by Name or Partial Name"></b-input>
                        </b-field>
                    </div>
                    <div class="column is-3" v-if="$root.geo.lat && $root.geo.lng">
                            <b-select v-model="search.distance" placeholder="Select Distance">
                                <option
                                        v-for="option in distances"
                                        :value="option.id"
                                        :key="option.id">
                                    {{option.text }}
                                </option>
                            </b-select>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <button class="button is-success" @click="getPlaces">Search</button>
                        <button class="button is-info" @click="reset">Reset Filters</button>

                    </div>
                </div>
                <p class="has-text-black heading">Searching for:
                    <span v-if="!search.name && !search.glutenFree && !search.vegan">All Locations</span>
                    <span v-else>
                    <span v-if="search.name">Places where the name has the term "{{search.name}}" in it.</span>
                </span>
                </p>
            </div>
        </b-collapse>
        <div v-if="!loading">
            <p  class="has-text-white" >Found <strong class="has-text-info">{{totalResults}}</strong> results.</p>
        </div>


        <div class="tile is-ancestor" v-if="!loading">
            <div class="tile is-parent is-4" v-for="item in items" :key="item.id">
                <article class="tile is-child box is-primary has-background-black-bis">
                        <h3 class="title has-text-primary is-size-4 listing-title">
                            <router-link :to="{name:'listing',params:{id:item.id},query:{ref:'search'}}">{{item.name}}</router-link>
                            <br>
                            <sup>
                                <span class="is-size-7 has-text-success" v-if="item.is_open">(Open Now!)</span>
                                <span class="is-size-7 has-text-danger" v-else>(Closed Now)</span>
                            </sup>
                        </h3>
                    <p class="heading has-text-white"><i class="fa fa-map-marker"></i>&nbsp;Located at {{item.address}} in {{item.city}}, {{item.state_code}}
                    <p class="has-text-white heading" v-if="item.user_distance">About <span class="has-text-success">{{item.user_distance}}</span> miles away.
                    <span class="has-text-success" v-if="item.user_distance <0.75">
                        <i class="fas fa-walking"></i>
                        <span class="is-hidden-mobile">(Walking Distance)</span>
                    </span>
                    <span class="has-text-success" v-else-if="item.user_distance <3">
                        <i class="fa fa-bicycle"></i>
                        <span class="is-hidden-mobile">(Biking Distance)</span>
                    </span>
                    <span class="has-text-success" v-else>
                        <i class="fa fa-car"></i>
                        <span class="is-hidden-mobile">(Driving Distance)</span>
                    </span>

                </p>
                    <p v-if="item.summary" class="subtitle has-text-white is-size-6">{{item.summary}}
                    </p>
                        <div v-else class="has-text-white description-prompt">
                            <p class="is-italic is-size-6"><em>We don't have a description for this place yet.</em></p>
                            <a class="button is-info is-outlined is-small" @click="$root.showDescriptionSuggestionModal(item.id,item.name)">Suggest a Description</a>
                        </div>
                        <router-link :to="{name:'listing',params:{id:item.id},query:{ref:'search'}}" class="button is-link is-outlined block is-fullwidth">See Complete Listing</router-link>

                </article>
            </div>
        </div>
        <div class="hero is-fullheight" v-else>
            <div class="hero-body">
                <div class="container has-text-centered">
                    <h1 class="has-text-white">
                        Loading your local restaurants...
                    </h1>
                </div>
            </div>
        </div>
        <b-loading :is-full-page="true" :active.sync="loading" :can-cancel="false"></b-loading>

    </div>
        <div class="has-background-grey-darker">
            <div class="container">
                <b-pagination
                        :total="totalResults"
                        :current.sync="currentPage"
                        size="is-medium"
                        order="is-centered"
                        icon-pack="fas"
                        :simple="false"
                        :rounded="true"
                        :per-page="perPage"
                        @change="loadNext"
                >
                </b-pagination>
            </div>

        </div>
    </div>
</template>
<script>
    export default {
        data(){
            return{
                tag:{
                    id:null,
                    name:''
                },
                search:{
                    name:null,
                    vegan:false,
                    glutenFree:false,
                    lat:null,
                    lng:null,
                    distance:null

                },
                loading:false,
                items:[],
                currentPage:1,
                totalResults:1,
                perPage:20,
                filtersVisible:true,
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

        methods: {
            /**
             * Make the API call to get the places that match the user's search
             * */
            getPlaces(){
                this.loading = true;
                this.search.lat=this.$root.geo.lat;
                this.search.lng=this.$root.geo.lng;
                let params = {
                    params:{
                        page:this.currentPage,
                        name:this.search.name,
                        lat:this.search.lat,
                        lng:this.search.lng,
                        distance:this.search.distance
                    }
                };
                axios.get('/api/places/search',params)
                    .then((response)=> {
                        this.scrollToTop();
                        this.loading = false;
                        this.items=response.data.data;
                        this.totalResults=response.data.total;
                        this.perPage=response.data.per_page;

                    })
                    .catch((error)=> {
                        this.scrollToTop();

                        this.loading = false;
                        this.$root.showNotification('We encountered an error and were unable to load your search results. If this problem persists, try refreshing the page.','danger');


                    });
                //always try to update the position--even if it isn't available in time for this request, we'll have it for the next ones
                this.$root.getUserLocation();
            },//findRandomPlace
            loadNext(next)
            {
                this.currentPage=next;
                this.getPlaces();
            },
            /**
             * Reset the form to the initial state
             */
            reset()
            {
             this.search={
                name:null,
                    vegan:false,
                    glutenFree:false

                };
                this.currentPage=1;
                this.totalResults=1;
                this.perPage=20;
                this.item=[];
                this.getPlaces();
            },//reset
            scrollToTop() {
                window.scrollTo(0,0);
            }
        },

        activated(){
            this.getPlaces()
        }
    }
</script>
<style>
    .tile.is-ancestor
    {
        flex-wrap:wrap;
    }
    .description-prompt
    {
        margin-bottom:24px;
        margin-top:-20px
    }
    .listing-title
    {
        margin-bottom: 0!important;
    }
    .tile.is-ancestor
    {
        padding-bottom: 20px;
    }
    .pagination-next, .pagination-link, .pagination-previous
    {
        color:#fff;
    }
    .panel-form-heading
    {
        margin-left:-10px ;
        margin-right:-10px ;
    }


</style>