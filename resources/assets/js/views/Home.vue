
<template>
    <v-content>
        <v-layout class="white--text" fill-height>
            <v-container class="max-500">
                <h1 class="white--text mb-2 display-1 text-xs-center">Eat Local ICT</h1>
                <p class="text-xs-center">What are you in the mood for?</p>
                <v-autocomplete
                        dark
                        :items="items"
                        hint="Click the icon to save"
                        v-model="selectedTag"
                        class="mx-3"
                        flat
                        block
                        label="Search by Tag"
                ></v-autocomplete>


                <p class="text-xs-center">- OR -</p>
            <v-btn color="success" :loading="loadingRandom" block @click.native="findRandomPlace">
                    Random Place
                </v-btn>

            </v-container>

        </v-layout>
        <v-footer dark class="pa-2" >
            <v-container class="pa-0">
                <p class="text-xs-center mb-0">
                    Copyright &copy; 2018 By Nicholas Coates

                </p>
            </v-container>

        </v-footer>
        <v-dialog v-model="showPlaceModal" width="500">
            <v-card>
                <v-card-title class="headline blue lighten-1" primary-title>
                How about...
                </v-card-title>
                <v-card-text>
                    <h1 class="text-xs-center">{{randomPlace.name}}</h1>
                    <p class="text-xs-center">Located at: {{randomPlace.address}} {{randomPlace.city}}, {{randomPlace.state_code}}</p>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="error" flat @click="findRandomPlace" :loading="loadingRandom" >No, Try Again</v-btn>
                    <v-btn color="success" flat :to="{name:'listing',params:{id:randomPlace.id}}">Looks Good!</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-content>

</template>
<script>
    export default {
        data(){
            return{
                loading:false,
                items:window.tags,
                selectedTag:null,
                loadingRandom:false,
                randomPlace:{
                    id:0,
                    name:null,
                    address:null,
                    city:null,
                    state_code:null
                },
                showPlaceModal:false
            }
        },
        methods:{
           findRandomPlace()
            {
                this.loadingRandom = true;
                axios.get('/api/places/random')
                    .then((response)=> {
                        this.loadingRandom = false;
                        this.randomPlace = response.data;
                        this.showPlaceModal=true;

                    })
                    .catch((error)=> {
                        this.loadingRandom = false;

                    })
            }

        },

        created(){

        }
    }
</script>