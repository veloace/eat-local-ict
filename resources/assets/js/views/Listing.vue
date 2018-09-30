<template>

    <div class="container">
        <h1 style="padding-top: 30px;" class="title has-text-centered">{{listing.name}}
        </h1>
        <p class="subtitle has-text-centered">
            <a :href="listing.map_link" target="_blank" rel="nofollow noopener">
                <i class="fa fa-map-marker"></i>
                {{listing.address}} {{listing.city}}, {{listing.state_code}}
            </a>
        </p>
        <p>
            {{listing.summary}}
        </p>
        <p>Price:
               <span class="is-size-6">
                <i class=" has-text-success fa fa-dollar" v-for="i in listing.price" :key="i"></i><i class="fa fa-dollar has-text-grey-lighter" v-for="c in (5-listing.price)" :key="c"></i>
            </span>
        </p>

        <p>Rating:
            <span class=" is-size-6">
                <i class="fa fa-star fa-2x has-text-warning" v-for="d in Math.round(listing.rating)" :key="d"></i><i class="fa fa-star fa-2x has-text-grey-lighter" v-for="e in (5- Math.round(listing.rating))" :key="e"></i>
            </span>
        </p>




        <p class="buttons">

        <a v-if="listing.phone_number" :href="'tel:'+listing.phone_number" class="button is-small is-primary is-outlined">
            <span class="icon">
              <i class="fa fa-phone "></i>
            </span>
            <span>{{listing.phone_number}}</span>
        </a>

        <a v-if="listing.email_address" :href="'mailto:'+listing.email_address" class="button is-small is-success  is-outlined">
                      <span class="icon">

            <i class="fa fa-envelope "></i>
                           </span>
            <span>&nbsp;Email</span>
        </a>


        <a  v-if="listing.website_url" :href="listing.website_url" target="_blank" class="button is-small is-link  is-outlined">
                       <span class="icon">

            <i class="fa fa-code fa-2x"></i>
                            </span>
            <span>&nbsp;Website</span>
        </a>

        <a v-if="listing.menu_link" :href="listing.menu_link" target="_blank" class="button is-small is-info  is-outlined">
                       <span class="icon">

            <i class="fa fa-book fa-2x"></i>
                            </span>
            <span>&nbsp;Menu</span>
        </a>
        </p>
        <hr>
        <h2 class="has-text-centered subtitle">Hours</h2>
        <p class="has-text-centered subtitle has-text-success" v-if="listing.is_open">Open Now!</p>
        <p class="has-text-centered subtitle has-text--danger" v-else="">Closed Now</p>
        <ul style="padding-bottom:100px;">
            <li v-for="hour in listing.hours" class="has-text-centered">
                {{hour}}
            </li>
        </ul>

    </div>
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
                    summary:null,
                    city:null,
                    state_code:null,
                    map_link:null,
                    hours:null,
                    is_open:null,
                    price:null,
                    rating:5
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