<template>
    <div class="top-spacer">


    <div class="container has-text-white " style="margin-top: 30px;">

        <b-collapse class="card" :open.sync="filtersVisible" animation="translate-y">
            <div slot="trigger" class="card-header has-background-grey-darker	">
                <strong class="has-text-white card-header-title">Advanced Search Filters&nbsp;<i v-if="!filtersVisible" class="fas fa-plus"></i> <i v-else class="fas fa-times"></i></strong>
            </div>
            <div class="card-content has-background-grey-dark">
                <div class="content">
                <div class="columns">
                    <div class="column is-8">
                        <b-field >
                            <b-input  v-model="search.name" placeholder="Search by Name or Partial Name"></b-input>
                        </b-field>
                    </div>
                    <div class="column is-4" v-if="$root.geo.lat && $root.geo.lng">
                            <b-select v-model="search.distance" placeholder="Select Distance" :expanded="true">
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
                        <div class="column is-10">
                            <b-field label="Search by Tag(s)" custom-class="has-text-white">
                                <b-taginput
                                        v-model="search.tags"
                                        autocomplete
                                        icon="tags"
                                        icon-pack="fa"
                                        :data="filteredTags"
                                        field="name"
                                        closable
                                        placeholder="Start typing to select a tag"
                                        @typing="getFilteredTags"
                                >
                                </b-taginput>
                            </b-field>

                        </div>
                        <div class="column is-2">
                            <b-field label="Sort By" custom-class="has-text-white">

                                <b-select v-model="search.sort" placeholder="Sort By" :expanded="true">
                                    <option
                                            v-for="option in sortOptions"
                                            :value="option.id"
                                            :key="option.id">
                                        {{option.text }}
                                    </option>
                                </b-select>
                            </b-field>
                        </div>
                        </div>
                    <p class="heading has-text-white">Service Options</p>

                    <div class="columns is-multiline">
                    <div class="column is-3">
                        <div class="field">
                            <b-switch class="has-text-white" size="is-small" :value="false" true-value="1" false-value="0" v-model="search.vegan">
                                Vegan Options
                            </b-switch>
                        </div>
                    </div>

                    <div class="column is-3">
                        <div class="field">
                            <b-switch class="has-text-white" size="is-small" :value="false" true-value="1" false-value="0" v-model="search.glutenFree">
                                Gluten-Free Options
                            </b-switch>
                        </div>
                    </div>

                    <div class="column is-3">
                        <div class="field">
                            <b-switch class="has-text-white" size="is-small" :value="false" true-value="1" false-value="0" v-model="search.alcohol">
                                Alcohol
                            </b-switch>
                        </div>
                    </div>

                        <div class="column is-3">
                            <div class="field">
                                <b-switch class="has-text-white" size="is-small" :value="false" true-value="1" false-value="0" v-model="search.meals">
                                    Full Meals
                                </b-switch>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field">
                                <b-switch class="has-text-white" size="is-small" :value="false" true-value="1" false-value="0" v-model="search.brunch">
                                     Brunch
                                </b-switch>
                            </div>
                        </div>

                        <div class="column is-3">
                            <div class="field">
                                <b-switch class="has-text-white" size="is-small" :value="false" true-value="1" false-value="0" v-model="search.carryout">
                                    Carryout/Drive-thru
                                </b-switch>
                            </div>
                        </div>

                        <div class="column is-3">
                            <div class="field">
                                <b-switch class="has-text-white" size="is-small" :value="false" true-value="1" false-value="0" v-model="search.delivery">
                                    Delivery
                                </b-switch>
                            </div>
                        </div>

                    </div>

                <p class="heading has-text-white">Accommodations</p>
                <div class="columns is-multiline">
                    <div class="column is-3">
                        <div class="field">
                            <b-switch class="has-text-white" size="is-small" :value="false" true-value="1" false-value="0" v-model="search.wifi">
                                Public Wi-Fi
                            </b-switch>
                        </div>
                    </div>

                    <div class="column is-3">
                        <div class="field">
                            <b-switch class="has-text-white" size="is-small" :value="false" true-value="1" false-value="0" v-model="search.parking">
                               Free Parking
                            </b-switch>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <b-switch class="has-text-white" size="is-small" :value="false" true-value="1" false-value="0" v-model="search.bikeRack">
                                Bike Rack Close By
                            </b-switch>
                        </div>
                    </div>


                    <div class="column is-3">
                        <div class="field">
                            <b-switch class="has-text-white" size="is-small" :value="false" true-value="1" false-value="0" v-model="search.charger">
                                EV Charger Close By
                            </b-switch>
                        </div>
                    </div>
                </div>

                    <div class="columns">
                    <div class="column">
                        <button class="button is-success" @click="getPlaces">Search</button>
                        <button class="button is-info" @click="reset">Reset Filters</button>

                    </div>
                </div>
                </div>
            </div>
        </b-collapse>
        <div v-if="!loading">
            <p  class="has-text-white" >Found <strong class="has-text-info">{{totalResults}}</strong> results.</p>
        </div>
        <p class="has-text-info heading">
          {{searchLabel}}
        </p>

        <div class="tile is-ancestor" v-if="!loading && items.length>0">
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
        <div v-else-if="!loading && items.length<1" class="hero is-fullheight">
            <header>
                <h2 class="title has-text-centered has-text-warning">No Results Found</h2>
                <p class="has-text-centered has-text-warning">We don't seem to have what you're looking for, try broadening your search and trying again.</p>
                <p class="has-text-centered has-text-white is-size-4">Do you think we're missing a restaurant?</p>
                <p class="has-text-centered"><a class="button is-info is-outlined" @click="showMissingPlaceModal=true">Let Us Know What We're Missing</a></p>
            </header>

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

        <b-modal :active.sync="showMissingPlaceModal">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <h2 class="modal-card-title">What are we missing?</h2>
                </header>
                <div class="modal-card-body">
                    <p class="has-text-white">Describe what you think we're missing (is it a place? Are the attributes wrong? Something else?). We will review suggestions and make any necessary edits. Thanks!</p>
                    <b-field >
                        <b-input type="textarea" v-model="missingSuggestion" placeholder="Ex. I searched for vegan places but Beautify Day Cafe didn't show up."></b-input>
                    </b-field>
                </div>
                <footer class="modal-card-foot">
                    <button @click="submitMissingPlaceSuggestion" class="button is-small is-success">Submit Suggestion</button>
                    <button class="button is-danger is-small" @click="showMissingPlaceModal=false">Cancel</button>
                </footer>
            </div>
            <button class="modal-close is-large" aria-label="close" @click="showMissingPlaceModal=false"></button>
        </b-modal>
    </div>
</template>
<script>
    export default {
        data(){
            return{
                showMissingPlaceModal:false,
                filteredTags:this.$root.tags,
                search:{
                    name:null,
                    lat:null,
                    lng:null,
                    distance:0,
                    sort:3,
                    vegan:0,
                    glutenFree:0,
                    alcohol:0,
                    wifi:0,
                    bikeRack:0,
                    meals:0,
                    carryout:0,
                    brunch:0,
                    delivery:0,
                    charger:0,
                    parking:0,
                    tags:[]

                },
                searchLabel:'Searching for all locations.',
                loading:false,
                items:[],
                currentPage:1,
                totalResults:1,
                perPage:20,
                filtersVisible:false,
                genres:[
                    {
                        id:1,
                        name:'American'
                    },
                    {
                        id:2,
                        name:'Vietnamese'
                    },
                ],
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

                ],
                sortOptions:[
                    {
                        id:1,
                        text:'Name, A-Z'
                    },
                    {
                        id:2,
                        text:'Name, Z-A'
                    },
                    {
                        id:3,
                        text:'Closest First'
                    },
                    {
                        id:4,
                        text:'Furthest First'
                    },

                ],
                missingSuggestion:null
            }
        },

        methods: {
            /**
             * Make the API call to get the places that match the user's search
             * */
            getPlaces(forReset = false){
                this.loading = true;
                this.search.lat=this.$root.geo.lat;
                this.search.lng=this.$root.geo.lng;
                let params = {
                        page:this.currentPage,
                        name:this.search.name,
                        lat:this.search.lat,
                        lng:this.search.lng,
                        distance:this.search.distance,
                        sort:this.search.sort,
                        vegan:this.search.vegan,
                        glutenFree:this.search.glutenFree,
                        alcohol:this.search.alcohol,
                        wifi:this.search.wifi,
                        bikeRack:this.search.bikeRack,
                        meals:this.search.meals,
                        carryout:this.search.carryout,
                        charger:this.search.charger,
                        brunch:this.search.brunch,
                        delivery:this.search.delivery,
                        parking:this.search.parking,
                        tags:this.convertTagsToIDs(this.search.tags)


                };
                console.log(params.tags);
                this.generateSearchLabel();
                this.scrollToTop();
                axios.get('/api/places/search',{params:params})
                    .then((response)=> {
                        this.loading = false;
                        this.items=response.data.data;
                        this.totalResults=response.data.total;
                        this.perPage=response.data.per_page;
                        this.filtersVisible=false;

                    })
                    .catch((error)=> {
                        this.filtersVisible=false;
                        this.loading = false;
                        this.$root.showNotification('We encountered an error and were unable to load your search results. If this problem persists, try refreshing the page.','danger');

                    });
            },//findRandomPlace
            generateSearchLabel()
            {
                let labelString='Searching for all locations';
                if(this.search.name)
                {
                    labelString='Searching for locations containing the term "'+this.search.name+'"';
                }
                if(this.search.distance!==0)
                {
                    labelString = labelString + " "+this.distances[this.search.distance].text+ ' from your current location'
                }

                let options = [];

                if(this.search.vegan==1)
                {
                    options.push(' have vegan options');
                }
                if(this.search.glutenFree==1)
                {
                    options.push(' have gluten-free options');

                }
                if(this.search.alcohol==1)
                {
                    options.push(' serves alcohol');

                }
                if(this.search.wifi==1)
                {
                    options.push(' have public wi-fi');

                }

                if(this.search.bikeRack==1)
                {
                    options.push(' have a bike rack close by');

                }
                if(this.search.meals==1)
                {
                    options.push(' serve full meal (breakfast, lunch, or dinner)');
                }
                if(this.search.carryout==1)
                {
                    options.push(' have carryout');

                }

                if(this.search.charger==1)
                {
                    options.push(' have an electric vehicle (EV) charger within walking distance');
                }

                if(this.search.brunch==1)
                {
                    options.push(' serve brunch');
                }

                if(this.search.delivery==1)
                {
                    options.push(' have delivery');
                }
                if(this.search.parking==1)
                {
                    options.push(' have free parking close by');

                }

                if(options.length>0)
                {
                    labelString = labelString+" that" + options.join(' and');
                }

                labelString=labelString+'. ';

                switch(this.search.sort)
                {
                    case 1:
                        labelString = labelString+'Sorted alphabetically by name, A-Z.';
                        break;
                    case 2:
                        labelString = labelString+'Sorted alphabetically by name, Z-A.';
                        break;
                    case 3:
                        labelString = labelString+'Sorted by distance, closest first.';
                        break;
                    case 4:
                        labelString = labelString+'Sorted by distance, furthest locations first.';
                        break;
                    default:
                        labelString = labelString+'Sorted by distance, closest first.';
                }

                this.searchLabel=labelString;
            },
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
                 lat:this.$root.geo.lat,
                 lng:this.$root.geo.lng,
                 distance:0,
                 sort:3,
                 genre:null,
                 vegan:0,
                 glutenFree:0,
                 alcohol:0,
                 wifi:0,
                 bikeRack:0,
                 meals:0,
                 brunch:0,
                 delivery:0,
                 charger:0,
                 parking:0,
                 tags:[]
             };
                this.currentPage=1;
                this.totalResults=1;
                this.perPage=20;
                this.item=[];
                this.getPlaces(true);
            },//reset
            scrollToTop() {
                window.scrollTo(0,0);
            },
            /**
             *
             */
            submitMissingPlaceSuggestion()
            {
                    this.$root.loading=true;
                    axios.post('/api/places/missing',{
                       description:this.missingSuggestion
                    })
                        .then((response) => {
                            this.$root.loading = false;

                        })
                        .catch((error) => {
                            this.$root.loading = false;

                        });

                    /*always do this, we really don't care about the response from the server as the user shouldn't have to be
                    worried about it--and there is nothing they can do if it fails, either.
                     */
                    this.missingSuggestion = null;
                    this.$root.showNotification('Thanks, we appreciate people like you! We have received your suggestion and will review its suitability for use on EatLocalICT! ','success')
                    this.showMissingPlaceModal=false;
            },
            convertTagsToIDs(tags)
            {
                let returnable = [];
                tags.forEach((tag)=>{
                    returnable.push(tag.id);
                });

                return returnable;
            },
            getFilteredTags(text)
            {
                this.filteredTags = this.$root.tags.filter((option) => {
                    return option.name
                        .toString()
                        .toLowerCase()
                        .indexOf(text.toLowerCase()) >= 0
                })
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

</style>