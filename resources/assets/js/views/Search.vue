<template>
    <div class="has-background-translucent top-spacer">

    <div class="container" style="margin-top: 30px;">
        <h1 class="has-text-center has-text-white title">Coming Soon!</h1>
        <p class="has-text-center has-text-white">In the meantime, check the progress of this and other planned features on our <router-link :to="{name:'about'}">master plan</router-link> page.</p>
        <p class="has-text-center has-text-white">Interested in how you can help out? Please see our<router-link :to="{name:'backer'}">backers</router-link> page.</p>

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
                category:{
                    id:null,
                    name:''
                },

                loading:false,
                items:[],
                page:1,
                totalPages:1
            }
        },

        methods: {
            getPlaces(page = null){
                this.loading = true;
                let url = '/api/places/tag/';
                if(this.selectedTag.id)
                {
                    url +=this.selectedTag.id;
                }

                axios.get(url+"?page="+this.page)
                    .then((response)=> {
                        this.loading = false;
                        this.items=response.data.data;
                        this.totalPages=response.data.total;

                    })
                    .catch((error)=> {
                        this.loading = false;

                    })
            },//findRandomPlace
        },
        activated(){


        }
    }
</script>