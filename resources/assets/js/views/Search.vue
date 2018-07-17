<template>
   <v-content>
       <v-container>
           <h1>Searching for {{selectedTag.name}} in Wichita, KS!</h1>

       </v-container>
   </v-content>
</template>
<script>
    export default {
        data(){
            return{
                selectedTag:{
                    id:null,
                    name:''
                }
            }
        },

        methods: {
            getPlaces(){

            }
        },
        activated(){

            this.selectedTag =window.tags.find(function(element) {
                let term = this.$route.params.term;
                return element.id == term;

            }.bind(this));
            this.getPlaces();
        }
    }
</script>