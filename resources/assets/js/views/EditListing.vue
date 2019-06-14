<template>
    <div class=" top-spacer">
        <div class="container has-text-white is-translucent" v-if="listing" style="margin-bottom: 15px;">
            <div class="columns">
                <div class="column" style="margin-top: 30px;">
                    <a @click="$router.go(-1)" class="button is-small is-link is-outlined">
                        <span class="icon">
                          <i class="fa fa-arrow-left "></i>
                        </span>
                        <span>Go Back</span>
                    </a>
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    <h1  class="title has-text-centered has-text-white" style="margin-bottom: 0">You are editing <span class="has-text-weight-bold has-text-success">{{listing.name}}</span>
                    </h1>
                    <div class="notification is-warning" v-if="listing.claim_status=='pending'">
                        Your ownership claim is still pending approval. You will be unable to make or save changes to this listing.
                    </div>
                </div>
            </div>

            <section class="columns is-multiline" >
                <div class="column is-half">
                    <div class="columns" style="margin-bottom: 0">
                        <div class="column" style="padding-bottom: 0">
                            <b-field label="Name" custom-class="has-text-white" >
                                <b-input :disabled="listing.claim_status!=='approved'" v-model="listing.name"  maxlength="75"></b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column" style="padding-top: 0">
                            <b-field label="Google Place ID (Uneditable)" custom-class="has-text-white" >
                                <b-input disabled v-model="listing.google_place_id"  maxlength="75"></b-input>
                            </b-field>
                        </div>
                    </div>

                </div>

                <div class="column is-half">
                    <b-field label="Short Description (This snippet appears in search results)" custom-class="has-text-white" >
                        <b-input :disabled="listing.claim_status!=='approved'" v-model="listing.summary"  maxlength="175" type="textarea"></b-input>
                    </b-field>
                </div>

                <div class="column is-full">
                    <hr>
                    <h1 class="has-text-white title is-4">Location Information</h1>
                </div>
                <div class="column is-two-fifths">
                    <b-field label="Street Address" custom-class="has-text-white" >
                        <b-input :disabled="listing.claim_status!=='approved'" v-model="listing.address"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-two-fifths">
                    <b-field label="City" custom-class="has-text-white" >
                        <b-input :disabled="listing.claim_status!=='approved'" v-model="listing.city"  maxlength="175" ></b-input>
                    </b-field>
                </div>
                <div class="column is-one-fifth">
                    <b-field label="State" custom-class="has-text-white" >
                        <b-input :disabled="listing.claim_status!=='approved'" v-model="listing.state_code"  maxlength="2" ></b-input>
                    </b-field>
                </div>
                <div class="column is-full">
                    <hr>
                    <h1 class="has-text-white title is-4">Hours of Operation</h1>
                </div>
                <div class="column is-full">
                   <p>Due to the complexity in managing hours of operation, we are currently pulling your open hours from the Google Places API.</p>
                    <p>In the future, we plan to add the ability for you to manage your hours from here, but that feature is still under development.</p>
                </div>
                <div class="column is-full">
                    <hr>
                    <h1 class="has-text-white title is-4">Contact & Social Media Information</h1>
                </div>

                <div class="column is-one-third">
                    <b-field label="Phone Number" custom-class="has-text-white" >
                        <b-input :disabled="listing.claim_status!=='approved'" v-model="listing.phone_number"  maxlength="10" ></b-input>
                    </b-field>
                </div>

                <div class="column is-one-third">
                    <b-field label="Email Address" custom-class="has-text-white" >
                        <b-input :disabled="listing.claim_status!=='approved'" v-model="listing.email_address"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-one-third">
                    <b-field label="Website URL" custom-class="has-text-white" >
                        <b-input :disabled="listing.claim_status!=='approved'" v-model="listing.website_url"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-one-third">
                    <b-field label="Link to Your Menu" custom-class="has-text-white" >
                        <b-input :disabled="listing.claim_status!=='approved'" v-model="listing.menu_link"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-one-third">
                    <b-field label="Facebook Page Name" custom-class="has-text-white" >
                        <b-input :disabled="listing.claim_status!=='approved'" v-model="listing.facebook_link"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-one-third">
                    <b-field label="Instagram Username" custom-class="has-text-white" >
                        <b-input :disabled="listing.claim_status!=='approved'" v-model="listing.instagram_link"  maxlength="175" ></b-input>
                    </b-field>
                </div>

                <div class="column is-full">
                    <hr>
                    <h1 class="has-text-white title is-4">Service Information</h1>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <span>Do you have any <em>vegan</em> options available to customers?</span>
                        <b-switch  :disabled="listing.claim_status!=='approved'"  size="is-small" true-value="1" v-model="listing.has_vegan_options">
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
                        <b-switch  :disabled="listing.claim_status!=='approved'"  size="is-small" true-value="1" v-model="listing.has_gluten_free_options">
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
                        <b-switch  :disabled="listing.claim_status!=='approved'"  size="is-small" true-value="1" v-model="listing.serves_alcohol">
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
                        <b-switch  :disabled="listing.claim_status!=='approved'"  size="is-small" true-value="1" v-model="listing.serves_full_meals">
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
                        <b-switch  :disabled="listing.claim_status!=='approved'"  size="is-small" true-value="1" v-model="listing.serves_brunch">
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
                        <b-switch  :disabled="listing.claim_status!=='approved'"  size="is-small" true-value="1" v-model="listing.has_carryout">
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
                        <b-switch   :disabled="listing.claim_status!=='approved'" size="is-small" true-value="1" v-model="listing.has_delivery">
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
                    <h1 class="has-text-white title is-4">Accommodations</h1>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <span>Do you have a physical store-front, or is this a food truck?</span>
                        <b-switch  :disabled="listing.claim_status!=='approved'"  size="is-small" true-value="1" v-model="listing.is_food_truck">
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
                        <b-switch  :disabled="listing.claim_status!=='approved'"  size="is-small" true-value="1" v-model="listing.has_public_wifi">
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
                             custom-class="has-text-white"
                    >
                        <b-input v-model="listing.wifi_password" maxlength="175"   :disabled="listing.claim_status!=='approved'" >
                        </b-input>
                    </b-field>
                </div>



                <div class="column is-full">
                    <div class="field">
                        <span>Is there a bike rack for secure storage of customer bicycles within 1 block?</span>
                        <b-switch  :disabled="listing.claim_status!=='approved'"  size="is-small" true-value="1" v-model="listing.has_bike_rack">
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
                        <b-switch  :disabled="listing.claim_status!=='approved'"  size="is-small" true-value="1" v-model="listing.has_free_parking">
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
                        <b-switch  :disabled="listing.claim_status!=='approved'" size="is-small" true-value="1" v-model="listing.has_ev_charger">
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
                    <b-field label="Enter some tags" custom-class="has-text-white">
                        <b-taginput
                                :disabled="listing.claim_status!=='approved'"
                                v-model="listing.tags"
                                autocomplete
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
                    <button  @click="save" class="button is-success" :disabled="listing.claim_status!=='approved'">Submit Changes</button>
                </div>

            </section>
        </div>

    </div>
</template>
<script>
    export default {

        data() {
            return {
                filteredTags:this.$root.tags,
                listing:null,
            }
        },//data
        methods:{
            loadListing()
            {
                this.$root.loading=true;
                axios.get('/api/places/owner/' + this.$route.params.id)
                    .then((response)=>{
                        this.listing= response.data;
                        this.$root.loading=false;
                    })
                    .catch((error)=>{
                        this.$root.loading=false;
                        if(error.response.status=422)
                        {
                            this.$root.showNotification(error.response.data,'danger')
                        }
                        else
                        {
                            this.$root.showNotification('We encountered an error and were unable to load the listing','danger')

                        }
                        this.$router.push({name:'account'})

                    })
            },
            save()
            {
                if(confirm("Are you sure you want to save your edits?")) {
                    axios.post('/api/places/owner/edit', this.listing)
                        .then((response) => {
                            this.$root.loading = false;
                            this.$root.showNotification('We saved your changes', 'success');
                            this.loadListing();//reload it

                        })
                        .catch((error) => {
                            this.$root.loading = false;
                            this.$root.showNotification('We encountered an error and were unable to update the listing', 'danger')

                        });
                }

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
            this.loadListing();
        }

    }
</script>