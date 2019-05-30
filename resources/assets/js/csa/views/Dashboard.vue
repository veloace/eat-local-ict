<template>
    <main class="container">
        <h1 class="title">Welcome to the EatLocalICT Admin Dashboard</h1>
        <div class="columns is-mobile">
            <div class="column has-text-centered is-half">
                <p class="heading">Total Users</p>
                <p class="is-size-3">{{users}}</p>
            </div>
            <div class="column has-text-centered is-half">
                <p class="heading">Total Places</p>
                <p class="is-size-3">{{places}}</p>
                <router-link :to="{name:'places'}" class="heading">Manage Places</router-link>
            </div>



        </div>
        <hr>
        <div class="notification is-warning" v-if="ownership_claims">
            You have {{ownership_claims}} ownership claims pending approval. <router-link :to="{name:'claims'}">View Them</router-link>
        </div>
        <div class="notification is-info " v-if="description_suggestions">
            You have {{description_suggestions}} place description suggestions pending approval. <a>View Them</a>
        </div>

        <div class="notification is-primary" v-if="missing_place_suggestions">
            You have {{missing_place_suggestions}} missing place suggestions pending approval. <a>View Them</a>
        </div>

    </main>
</template>
<script>
export default {

    data() {
        return {
            ownership_claims:null,
            description_suggestions: null,
            missing_place_suggestions: null,
            places: null,
            users: null,
        }
    },
    methods:{
        load()
        {
            this.$root.loading=true;
            axios.get('/webAPI/')
                .then((response)=>{
                    this.ownership_claims = response.data.ownership_claims;
                    this.description_suggestions = response.data.description_suggestions;
                    this.missing_place_suggestions=response.data.missing_place_suggestions;
                    this.places = response.data.places;
                    this.users=response.data.users;
                    this.$root.loading=false;

                })
                .catch((error)=>{
                    this.$root.loading=false;
                    this.$root.showNotification('Could not load dashboard.','danger');
                })
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
    }
</style>