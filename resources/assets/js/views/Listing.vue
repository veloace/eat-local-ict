<template>
    <div class="has-background-translucent top-spacer">
        <div v-if="!loading" class="container has-text-white">
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
                    <h1  class="title has-text-centered has-text-white">{{listing.name}}

                    </h1>
                    <p class="subtitle has-text-centered">
                        <a :href="listing.map_link" target="_blank" rel="nofollow noopener">
                            <i class="fa fa-map-marker"></i>
                            {{listing.address}} {{listing.city}}, {{listing.state_code}}
                        </a>
                        <span v-if="listing.user_distance" class="has-text-info">
                           (About {{listing.user_distance}} miles away)
                        </span>
                    </p>
                    <p class="subtitle has-text-centered">
                        <small class="has-text-centered subtitle has-text-success" v-if="listing.is_open">Open Now!</small>
                        <small class="has-text-centered subtitle has-text-danger" v-else="">Closed Now</small>
                    </p>
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    <p v-if="listing.summary">
                        {{listing.summary}}
                    </p>
                    <div v-else class="has-text-white">
                        <p class="is-italic"><em>We don't have a description for this place yet.</em></p>
                        <a class="button is-info is-outlined is-small" @click="suggestDescription">Suggest a Description</a>
                    </div>
                    <p>Price:
                        <span class="is-size-6">
                <i class=" has-text-success fas fa-dollar-sign" v-for="i in listing.price"></i><i
                                class="fas fa-dollar-sign has-text-grey-lighter" v-for="c in (5-listing.price)"></i>
            </span>
                    </p>

                    <p>Rating:
                        <span class=" is-size-6">
                <i class="fa fa-star fa-2x has-text-warning" v-for="d in Math.round(listing.rating)"></i><i
                                class="fa fa-star has-text-grey-lighter" v-for="e in (5- Math.round(listing.rating))"></i>
            </span>
                    </p>


                    <p class="buttons">
                        <a :href="listing.map_link" target="_blank" rel="nofollow noopener" class="button is-small is-danger is-outlined">Let's Go!
                        &nbsp;<i class="fa fa-location-arrow"></i>
                        </a>

                        <a v-if="listing.phone_number" :href="'tel:'+listing.phone_number"
                           class="button is-small is-primary is-outlined">
            <span class="icon">
              <i class="fa fa-phone "></i>
            </span>
                            <span>{{listing.phone_number}}</span>
                        </a>

                        <a v-if="listing.email_address" :href="'mailto:'+listing.email_address"
                           class="button is-small is-success  is-outlined">
                      <span class="icon">

            <i class="fa fa-envelope "></i>
                           </span>
                            <span>&nbsp;Email</span>
                        </a>


                        <a v-if="listing.website_url" :href="listing.website_url" target="_blank"
                           class="button is-small is-link  is-outlined">
                       <span class="icon">

            <i class="fa fa-code"></i>
                            </span>
                            <span>&nbsp;Website</span>
                        </a>

                        <a v-if="listing.menu_link" :href="listing.menu_link" target="_blank"
                           class="button is-small is-info  is-outlined">
                       <span class="icon">

            <i class="fa fa-book "></i>
                            </span>
                            <span>&nbsp;Menu</span>
                        </a>
                    </p>
                    <p>
                        <a v-if="listing.facebook_link" :href="'https://facebook.com/'+listing.facebook_link" target="_blank" rel="nofollow"><i style="color:#fff" class="fab fa-facebook fa-2x"></i></a>
                        <a v-if="listing.instagram_link" :href="'https://instagram.com/'+listing.instagram_link" target="_blank" rel="nofollow"><i style="color:#FFF" class="fab fa-instagram fa-2x"></i></a>
                    </p>
                </div>
                <div class="column">
                    <h2 class="has-text-centered subtitle has-text-white has-text-weight-bold">Hours</h2>
                    <ul>
                        <li v-for="(hour,idx) in listing.hours" class="has-text-centered">
                            <strong v-if="idx===today" class="has-text-weight-bold has-text-white">
                                <i class="fa fa-arrow-circle-right"></i>
                                {{hour}}
                            </strong>
                            <span v-else>{{hour}}</span>
                        </li>
                    </ul>

                </div>

            </div>
            <hr>
            <div v-if="listing.reviews">
                <h2  class="title has-text-centered has-text-white">Recent Google Reviews</h2>
                <article class="media"  v-for="review in listing.reviews">
                    <figure class="media-left">
                        <p class="image is-64x64">
                            <img :src="review.profile_photo_url">
                        </p>
                    </figure>
                    <div class="media-content">
                        <div class="content">
                            <p>
                                <a :href="review.author_url" target="_blank" rel="nofollow">{{review.author_name}}</a> <small>{{review.relative_time_description}}</small>
                                <br>
                                {{review.text}}
                            </p>
                            <p>

                                           <span class=" is-size-6">
                <i class="fa  fa-star has-text-warning" v-for="d in Math.round(review.rating)"></i><i
                                                   class="fa fa-xs fa-star has-text-grey-lighter" v-for="e in (5- Math.round(review.rating))"></i>
            </span>
                            </p>
                        </div>
                    </div>
                </article>
            </div>
            <hr>
            <a @click="$router.go(-1)"  class="button is-small is-link is-outlined" style="margin-bottom: 30px;">
                        <span class="icon">
                          <i class="fa fa-arrow-left "></i>
                        </span>
                <span>Go Back</span>
            </a>
        </div>
        <div class="hero is-fullheight" v-else>
            <div class="hero-body">
                <div class="container has-text-centered">
                    <h1 class="has-text-white">
                        <i class="fa fa-5x fa-spinner fa-spin"></i><br>
                        Loading your local eatery...
                    </h1>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            let d = new Date();
            switch(d.getDay())
            {
                case 0:
                    d = 6;
                    break;
                default:
                    d=d.getDay()-1;
            }
            return {
                loading: true,
                today:d,
                listing: {
                    id: null,
                    name: null,
                    address: null,
                    summary: null,
                    city: null,
                    state_code: null,
                    map_link: null,
                    hours: null,
                    is_open: null,
                    price: null,
                    rating: 5,
                    reviews:null,
                    facebook_link:null,
                    instagram_link:null,
                    user_distance:null
                },
                tags: [],
                lastPage: {
                    name: 'home',
                    params: {}
                }
            }
        },
        methods: {
            /**
             * Makes the API call to get this listing from the backend
             */
            getListing() {
                this.loading = true;

                let params = {params:{
                    'lat':this.$root.geo.lat,
                    'lng':this.$root.geo.lng
                    }};

                axios.get('/api/places/index/' + this.$route.params.id,params)
                    .then((response) => {
                        this.listing = response.data;
                        this.tags = [];
                        this.loading = false;

                    })
                    .catch((error) => {
                        this.loading = false;
                        this.$root.showNotification('We encountered an error and were unable to load a random place. If this problem persists, try refreshing the page.','danger');
                    })
            },
            suggestDescription(){
                this.$root.showDescriptionSuggestionModal(this.listing.id,this.listing.name);
            }
        },
        activated() {
            this.getListing();
            //
            //this removes the is-clipped class buefy uses for modals which breaks scrolling on this page
            let html = document.getElementsByTagName('html');
            html[0].classList.remove('is-clipped');

        }
    }
</script>