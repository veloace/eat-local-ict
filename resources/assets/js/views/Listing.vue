<template>
<v-layout row wrap class="white--text"t>
    <v-container class="max-800">

        <v-card v-if="!loading">
            <v-card-title class="headline blue lighten-1" primary-title>
                {{listing.name}}
            </v-card-title>
            <v-card-text>

                <v-layout row wrap align-center>
                    <v-flex class="text-xs-center">
                        <v-avatar color="grey lighten-4" size="120" class="text-xs-center">
                            <img v-if="listing.image_url" :src="listing.image_url" alt="avatar">
                            <img src="/img/ictFlag.svg" alt="avatar">
                        </v-avatar>
                    </v-flex>
                </v-layout>
                <p class="text-xs-center">
                    <a :href="listing.map_link">
                        <v-icon>
                            place
                        </v-icon>
                    </a>

                    {{listing.address}} {{listing.city}}, {{listing.state_code}}

                </p>
                <p class="text-xs-center">

                    <v-btn v-if="listing.phone_number" :href="'tel:'+listing.phone_number" flat icon color="pink" >
                        <v-icon>phone</v-icon>
                    </v-btn>

                    <v-btn v-if="listing.email_address" :href="'mailto:'+listing.email_address" flat icon color="pink">
                        <v-icon>mail</v-icon>
                    </v-btn>


                    <v-btn  v-if="listing.website_url" :href="listing.website_url" target="_blank" flat icon color="pink">
                        <v-icon>web</v-icon>
                    </v-btn>

                    <v-btn v-if="listing.menu_link" :href="listing.menu_link" target="_blank" flat icon color="pink">
                        <v-icon>restaurant</v-icon>
                    </v-btn>

                </p>
                <p>
                    {{listing.description}}
                </p>

                <v-divider></v-divider>
                <h2 class="text-xs-center">Hours</h2>
                <v-list>

                    <v-list-tile>
                        <v-list-tile-content>
                            <v-list-tile-title>Monday</v-list-tile-title>
                            <v-list-tile-sub-title>
                                <span class="green--text" v-if="listing.hours.Monday">
                                Open From {{listing.hours.Monday.open}} until {{listing.hours.Monday.close}}
                                </span>
                                <span class="red--text" v-else>CLOSED</span>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-divider></v-divider>

                    <v-list-tile>
                        <v-list-tile-content>
                            <v-list-tile-title>Tuesday</v-list-tile-title>
                            <v-list-tile-sub-title>
                                <span class="green--text" v-if="listing.hours.Tuesday">
                                Open From {{listing.hours.Tuesday.open}} until {{listing.hours.Tuesday.close}}
                                </span>
                                <span class="red--text" v-else>CLOSED</span>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-divider></v-divider>


                    <v-list-tile>
                        <v-list-tile-content>
                            <v-list-tile-title>Wednesday</v-list-tile-title>
                            <v-list-tile-sub-title>
                                <span class="green--text" v-if="listing.hours.Wednesday">
                                Open From {{listing.hours.Wednesday.open}} until {{listing.hours.Wednesday.close}}
                                </span>
                                <span class="red--text" v-else>CLOSED</span>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-divider></v-divider>


                    <v-list-tile>
                        <v-list-tile-content>
                            <v-list-tile-title>Thursday</v-list-tile-title>
                            <v-list-tile-sub-title>
                                <span class="green--text" v-if="listing.hours.Thursday">
                                Open From {{listing.hours.Thursday.open}} until {{listing.hours.Thursday.close}}
                                </span>
                                <span class="red--text" v-else>CLOSED</span>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-divider></v-divider>


                    <v-list-tile>
                        <v-list-tile-content>
                            <v-list-tile-title>Monday</v-list-tile-title>
                            <v-list-tile-sub-title>
                                <span class="green--text" v-if="listing.hours.Friday">
                                Open From {{listing.hours.Friday.open}} until {{listing.hours.Friday.close}}
                                </span>
                                <span class="red--text" v-else>CLOSED</span>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-divider></v-divider>

                    <v-list-tile>
                        <v-list-tile-content>
                            <v-list-tile-title>Monday</v-list-tile-title>
                            <v-list-tile-sub-title>
                                <span class="green--text" v-if="listing.hours.Saturday">
                                Open From {{listing.hours.Saturday.open}} until {{listing.hours.Saturday.close}}
                                </span>
                                <span class="red--text" v-else>CLOSED</span>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-divider></v-divider>

                    <v-list-tile>
                        <v-list-tile-content>
                            <v-list-tile-title>Sunday</v-list-tile-title>
                            <v-list-tile-sub-title>
                                <span class="green--text" v-if="listing.hours.Sunday">
                                Open From {{listing.hours.Sunday.open}} until {{listing.hours.Sunday.close}}
                                </span>
                                <span class="red--text" v-else>CLOSED</span>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                </v-list>
            </v-card-text>
            <v-divider></v-divider>
            <h2 class="text-xs-center">Tags</h2>
            <v-card-actions>
                <v-btn flat block color="warning" :to="lastPage">Go Back</v-btn>
                <v-spacer></v-spacer>
                <v-btn flat block color="success" :to="{name:'home'}">Start Over</v-btn>
            </v-card-actions>
        </v-card>
    </v-container>
</v-layout>
</template>
<script>
    export default {
        data(){
            return{
                loading:true,
                listing:{
                    id:null,
                    name:null,
                    address:null,
                    city:null,
                    state_code:null
                },
                tags:[],
                lastPage:{
                    name:'home',
                    params:{}
                }
            }
        },
        methods:{
            getListing()
            {
                this.loadingRandom = true;
                axios.get('/api/places/index/'+this.$route.params.id)
                    .then((response)=> {
                        this.loading = false;
                        this.listing = response.data;
                        tempTag=[];
                        this.tags=[];
                        console.log('TAGDS');
                        console.log(this.listing.tags);
                        this.listing.tags.forEach(function(t){
                            console.log(t);
                        });
                    })
                    .catch((error)=> {
                        this.loadingRandom = false;

                    })
            }
        },
        activated(){
            this.getListing();
        }
    }
</script>