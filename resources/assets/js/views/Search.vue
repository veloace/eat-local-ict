<template>
    <div class="container" style="margin-top: 75px">
        <h1 class="has-text-center has-text-white title">Coming Soon! <router-link :to="{name:'home'}">Go Back</router-link></h1>
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