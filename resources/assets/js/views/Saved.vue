<template>
    <div class="has-background-translucent top-spacer">
        <div class="container has-text-white has-background-translucent" style="margin-top: 25px;min-height: 100vh">
            <header>
                <h1 class="title has-text-white">Saved Locations</h1>
                <p class="heading">These are restaurants that you have marked as "Save for Later"</p>
            </header>
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
                        <button class="button is-danger is-outlined block is-fullwidth" @click="removeFromSaved(item)">Remove From List</button>
                    </article>
                </div>
            </div>
            <div v-else-if="!loading && items.length<1" class="hero is-fullheight">
                <header>
                    <h2 class="title has-text-centered has-text-warning">No Results Found</h2>
                    <p class="has-text-centered has-text-warning">It doesn't look like you have any restaurants saved yet.</p>
                    <p class="has-text-centered"><router-link :to="{name:'search'}" class="button is-info is-outlined">Find Local Restaurants</router-link></p>
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

    </div>
    </div>
</template>
<script>
    export default {
        data(){
            return {
                loading:false,
                items:[],
                currentPage:1,
                totalResults:1,
                perPage:20,
            };
        },
        methods: {
            loadSaved() {
                this.loading = true;
                let params = {
                    params: {
                        page: this.currentPage,
                        saved: 1
                    }
                };
                axios.get('/api/places/search', params)
                    .then((response) => {
                        this.scrollToTop();
                        this.loading = false;
                        this.items = response.data.data;
                        this.totalResults = response.data.total;
                        this.perPage = response.data.per_page;

                    })
                    .catch((error) => {
                        this.scrollToTop();

                        this.loading = false;
                        this.$root.showNotification('We encountered an error and were unable to load your favorites. If this problem persists, try refreshing the page.', 'danger');


                    });
            },
            removeFromSaved(place) {
                if(confirm("Remove "+place.name+" from this list?")) {


                    this.loading = true;
                    axios.post('/api/places/lists/savedForLater/delete', {id: place.id})
                        .then((response) => {
                            this.loading = false;
                            this.$root.showNotification('Removed '+place.name+' from your list.', 'success');

                            for (let key in this.items) {
                                if(this.items[key].id===place.id)
                                {
                                    console.log('it works...');
                                    this.items.splice(key,1);
                                }
                            }
                        })
                        .catch((error) => {
                            this.scrollToTop();
                            this.loading = false;
                            this.$root.showNotification('We encountered an error and were unable to delete this item from your list. If this problem persists, try refreshing the page.', 'danger');
                        });
                }
            },
            scrollToTop() {
                window.scrollTo(0, 0);
            },
            loadNext(next) {
                this.currentPage = next;
                this.loadSaved();
            },
        },
        activated(){
            this.loadSaved();
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