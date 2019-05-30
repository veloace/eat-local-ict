<template>
<main class="container ">
    <div class="columns is-multiline" v-if="places.length>0">
        <div class="column is-one-quarter listing" v-for="place in places" :key="place.id">
            <h1 class="has-text-weight-bold">{{place.name}}</h1>
            <p class="heading">{{place.address}} {{place.city}}, {{place.state_code}}</p>
            <p>
                <span class="tag is-warning" v-if="place.email_address==null">NO EMAIL</span>
                <span class="tag is-warning" v-if="place.phone_number==null">NO PHONE</span>
                <span class="tag is-danger" v-if="place.summary==null">NO DESCRIPTION</span>
                <span class="tag is-danger" v-if="place.tags.length<1">NO TAGS</span>
            </p>
            <hr>
            <p>
                <router-link :to="{name:'editPlace',params:{id:place.id}}" class="button is-info is-small">Edit Listing</router-link>
            </p>
        </div>
    </div>
        <div class="">
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
</main>
</template>
<script>
    export default {

        data() {
            return {
                places:[],
                currentPage:1,
                totalResults:1,
                perPage:50,
            }
        },
        methods: {
            load() {
                this.$root.loading = true;
                axios.get('/webAPI/place?page='+this.currentPage)
                    .then((response) => {
                        this.$root.loading = false;
                        this.places = response.data.data;
                        this.totalResults=response.data.total;
                        this.perPage=response.data.per_page;
                    })
                    .catch((error) => {
                        this.$root.loading = false;
                        this.$root.showNotification('Could not load places.', 'danger');
                    })
            },
            loadNext(next)
            {
                this.currentPage=next;
                this.load();
            }
        },
        activated(){
            this.load();
        }
    }
</script>
<style>
    main.container
    {
        margin-top:60px;
        margin-bottom:60px;
    }
    .listing
    {
        border: 1px solid #848484;
    }

</style>