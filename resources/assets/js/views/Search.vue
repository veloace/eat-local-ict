<template>
 <div class="container">
     <h1 class="is-size-3-desktop is-size-5-mobile">Search For Local Food & Drink in Wichita</h1>
     <section class="message is-info">
         <div class="message-header">
             <p>Search Filters</p>
         </div>
         <div class="message-body">
           <div class="columns">
               <div class="column">
                   <div class="field">
                       <label class="label">Category</label>
                       <div class="control">
                           <select class="input" >
                               <option selected disabled>Select a Category</option>
                           </select>
                       </div>
                   </div>
               </div>
               <div class="column">
                   <div class="field">
                       <label class="label">Tag</label>
                       <div class="control">
                           <input class="input" type="text" placeholder="Type to Search By Tag">
                       </div>
                   </div>
               </div>

           </div>
         </div>
     </section>
     <section class="top-spacer">
        <h2>Search Results</h2>
         <div v-if="loading" class="top-spacer">
             <p class="has-text-centered">
                 <i class="fa fa-spinner fa-spin fa-4x"></i>
             </p>
             <p class="has-text-centered">
                 Loading Results...Please Wait!
             </p>
         </div>
         <p class="has-text-centered" v-else-if="items.length==0">No results found. Please adjust the filters above and click 'Search'.</p>
     </section>
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