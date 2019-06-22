<template>
    <main class="container">
        <section class="columns">
            <div class="column">
                <router-link v-if="previous!==null" :to="{name:'editPlace',params:{id:previous}}"  class="button is-info"> <i class="fa fa-arrow-left "></i>&nbsp;Previous Listing</router-link>
            </div>
            <div class="column has-text-centered">
                <router-link :to="{name:'places'}"  class="button is-info">Go Back to Place List&nbsp;</router-link>

            </div>
            <div class="column has-text-right">
                <router-link v-if="next!==null"  :to="{name:'editPlace',params:{id:next}}"  class="button is-info">Next Listing&nbsp;<i class="fa fa-arrow-right "></i></router-link>
            </div>
        </section>
        <div class="" v-if="listing" style="margin-bottom: 15px;">

            <div class="columns">
                <div class="column">
                    <h1  class="title has-text-centered " style="margin-bottom: 0">You are editing <span class="has-text-weight-bold has-text-success">{{listing.name}}</span>
                    </h1>
                 
                </div>
            </div>

            <div class="columns is-multiline" >
                <div class="column is-half">
                    <div class="columns" style="margin-bottom: 0">
                        <div class="column" style="padding-bottom: 0">
                            <b-field label="Name" custom-class="" >
                                <b-input  v-model="listing.name"  maxlength="75"></b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column" style="padding-top: 0">
                            <b-field label="Google Place ID (Probably doesn't need to be changed)" custom-class="" >
                                <b-input  v-model="listing.google_place_id"  maxlength="75"></b-input>
                            </b-field>
                        </div>
                    </div>

                </div>

                <div class="column is-half">
                    <b-field label="Short Description (This snippet appears in search results)" custom-class="" >
                        <b-input  v-model="listing.summary"  maxlength="175" type="textarea"></b-input>
                    </b-field>
                </div>

                <div class="column is-full">
                    <hr>
                    <h1 class=" title is-4">Location Information</h1>
                </div>
                <div class="column is-two-fifths">
                    <b-field label="Street Address" custom-class="" >
                        <b-input  v-model="listing.address"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-two-fifths">
                    <b-field label="City" custom-class="" >
                        <b-input  v-model="listing.city"  maxlength="175" ></b-input>
                    </b-field>
                </div>
                <div class="column is-one-fifth">
                    <b-field label="State" custom-class="" >
                        <b-input  v-model="listing.state_code"  maxlength="2" ></b-input>
                    </b-field>
                </div>

                <div class="column is-full">
                    <hr>
                    <h1 class=" title is-4">Contact & Social Media Information</h1>
                </div>

                <div class="column is-one-third">
                    <b-field label="Phone Number" custom-class="" >
                        <b-input  v-model="listing.phone_number"  maxlength="10" ></b-input>
                    </b-field>
                </div>

                <div class="column is-one-third">
                    <b-field label="Email Address" custom-class="" >
                        <b-input  v-model="listing.email_address"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-one-third">
                    <b-field label="Website URL" custom-class="" >
                        <b-input  v-model="listing.website_url"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-one-third">
                    <b-field label="Link to Your Menu" custom-class="" >
                        <b-input  v-model="listing.menu_link"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-one-third">
                    <b-field label="Facebook Page Name" custom-class="" >
                        <b-input  v-model="listing.facebook_link"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-one-third">
                    <b-field label="Instagram Username" custom-class="" >
                        <b-input  v-model="listing.instagram_link"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-one-third">
                    <b-field label="WichitaByEb Link" custom-class="" >
                        <b-input  v-model="listing.eb_review_link"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-full">
                    <hr>
                    <h1 class=" title is-4">Service Information</h1>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <span>Do you have any <em>vegan</em> options available to customers?</span>
                        <b-switch    size="is-small" true-value="1" v-model="listing.has_vegan_options">
                            <span v-if="listing.has_vegan_options">
                                YES
                            </span>

                            <span v-else>
                                NO
                            </span>
                        </b-switch>
                    </div>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <span>Do you have any <em>gluten-free</em> options available to customers?</span>
                        <b-switch    size="is-small" true-value="1" v-model="listing.has_gluten_free_options">
                            <span v-if="listing.has_gluten_free_options">
                                YES
                            </span>
                            <span v-else>
                                NO
                            </span>
                        </b-switch>
                    </div>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <span>Do you serve alcohol?</span>
                        <b-switch    size="is-small" true-value="1" v-model="listing.serves_alcohol">
                            <span v-if="listing.serves_alcohol">
                                YES
                            </span>
                            <span v-else>
                                NO
                            </span>
                        </b-switch>
                    </div>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <span>Do you serve full meals (breakfast, lunch, or dinner)?</span>
                        <b-switch    size="is-small" true-value="1" v-model="listing.serves_full_meals">
                            <span v-if="listing.serves_full_meals">
                                YES
                            </span>
                            <span v-else>
                                NO
                            </span>
                        </b-switch>
                    </div>
                </div>


                <div class="column is-full">
                    <div class="field">
                        <span>Do you serve brunch on any day of the week?</span>
                        <b-switch    size="is-small" true-value="1" v-model="listing.serves_brunch">
                            <span v-if="listing.serves_brunch">
                                YES
                            </span>
                            <span v-else>
                                NO
                            </span>
                        </b-switch>
                    </div>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <span>Do you have carryout or a drive-thru?</span>
                        <b-switch    size="is-small" true-value="1" v-model="listing.has_carryout">
                            <span v-if="listing.has_carryout">
                                YES
                            </span>
                            <span v-else>
                                NO
                            </span>
                        </b-switch>
                    </div>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <span>Do you have delivery, either in-house or through a third-party service like Uber Eats?</span>
                        <b-switch    size="is-small" true-value="1" v-model="listing.has_delivery">
                            <span v-if="listing.has_delivery">
                                YES
                            </span>
                            <span v-else>
                                NO
                            </span>
                        </b-switch>
                    </div>
                </div>

                <div class="column is-full">
                    <hr>
                    <h1 class=" title is-4">Accommodations</h1>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <span>Do you have a physical store-front, or is this a food truck?</span>
                        <b-switch    size="is-small" true-value="1" v-model="listing.is_food_truck">
                            <span v-if="listing.is_food_truck">
                                FOOD TRUCK
                            </span>
                            <span v-else>
                                STOREFRONT
                            </span>
                        </b-switch>
                    </div>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <span>Do you have public Wi-Fi available to customers?</span>
                        <b-switch    size="is-small" true-value="1" v-model="listing.has_public_wifi">
                            <span v-if="listing.has_public_wifi">
                                YES
                            </span>
                            <span v-else>
                                NO
                            </span>
                        </b-switch>
                    </div>
                </div>

                <div class="column is-full" v-if="listing.has_public_wifi">
                    <b-field label="Wi-Fi Password (OPTIONAL)"
                             message="Provide your public wi-fi password to make life easier for your customers. IMPORTANT NOTICE: IF you chose to publish the wi-fi password, keep in mind that people will no longer have to physically be in your store to get access to your Wi-Fi."
                             custom-class=""
                    >
                        <b-input v-model="listing.wifi_password" maxlength="175"    >
                        </b-input>
                    </b-field>
                </div>



                <div class="column is-full">
                    <div class="field">
                        <span>Is there a bike rack for secure storage of customer bicycles within 1 block?</span>
                        <b-switch    size="is-small" true-value="1" v-model="listing.has_bike_rack">
                            <span v-if="listing.has_bike_rack">
                                YES
                            </span>
                            <span v-else>
                                NO
                            </span>
                        </b-switch>
                    </div>
                </div>



                <div class="column is-full">
                    <div class="field">
                        <span>Is there free parking available within a reasonable distance?</span>
                        <b-switch    size="is-small" true-value="1" v-model="listing.has_free_parking">
                            <span v-if="listing.has_free_parking">
                                YES
                            </span>
                            <span v-else>
                                NO
                            </span>
                        </b-switch>
                    </div>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <span>Is there a public electric vehicle (EV) charging station within walking distance?</span>
                        <b-switch   size="is-small" true-value="1" v-model="listing.has_ev_charger">
                            <span v-if="listing.has_ev_charger">
                                YES
                            </span>
                            <span v-else>
                                NO
                            </span>
                        </b-switch>
                    </div>
                </div>

                <div class="column is-full">
                    <b-field label="Enter some tags">
                        <b-taginput
                                v-model="listing.tags"
                                autocomplete
                                allow-new
                                icon="tags"
                                icon-pack="fa"
                                :data="filteredTags"
                                field="name"
                                closable
                                placeholder="Add a tag"
                                @typing="getFilteredTags"
                                >
                        </b-taginput>
                    </b-field>
                </div>
                <div class="column is-full has-text-centered">
                    <hr>
                    <button class="button is-success" @click="save">Submit Changes</button>
                </div>

            </div>
        </div>

    </main>
</template>
<script>
    export default {

        data() {
            return {
                filteredTags:[],
                listing:null,
                previous:null,
                next:null,
                tags:[]
            }
        },//data
        methods:{
            loadListing() {
                let id = this.$route.params.id;
                this.$root.loading=true;
                axios.get('/webAPI/place/edit/' + id)
                    .then((response)=>{
                        this.listing= response.data.listing;
                        this.previous= response.data.previous;
                        this.next= response.data.next;
                        this.tags= response.data.tags;
                        this.$root.loading=false;
                    })
                    .catch((error)=>{
                        this.$root.loading=false;
                        if(error.response.status=422)
                        {
                            this.$root.showNotification('We encountered an error and were unable to load the listing','danger')

                        }

                    })
            },
            save()
            {
                axios.post('/webAPI/place/edit',this.listing)
                    .then((response)=>{
                        this.$root.loading=false;
                        this.$root.showNotification('We saved your changes','success');
                        this.loadListing();//reload it

                    })
                    .catch((error)=> {
                        this.$root.loading = false;
                        this.$root.showNotification('We encountered an error and were unable to update the listing','danger')

                    });


            },
            getFilteredTags(text)
            {
                this.filteredTags = this.tags.filter((option) => {
                    return option.name
                        .toString()
                        .toLowerCase()
                        .indexOf(text.toLowerCase()) >= 0
                })
            }
        },
        watch: {
            '$route.params.id': function (id) {
                if(id)
                {
                    this.loadListing();

                }
            }
        },
        activated(){
            this.loadListing();
        }

    }
</script>
<style>
    main.container{
        margin-top: 60px;
    }
</style>