<template>
    <main class="container">
        <h1 class="title">EatLocalICT Place Ownership Claims</h1>
        <div v-for="claim in ownership_claims">
            <h2 class="is-size-5 has-text-weight-bold">{{claim.place.name}}</h2>
            <p>Ownership was claimed by <strong>{{claim.user.name}}</strong> ({{claim.user.email}}) on  {{claim.created_at}}.</p>
            <p v-if="claim.user.email_verified_at===null" class="has-text-danger">THIS USER HAS NOT VERIFIED THEIR EMAIL. YOU CANNOT APPROVE THIS REQUEST</p>
            <div v-else>
                <p>Please manually verify the ownership of this user below filling out the below portion. This can include, but is not limited to, emailing or calling the place to verify ownership, physically visiting the place, etc.</p>

                <b-field label="Comments (why this claim was rejected or accepted). The user can see this.">
                    <b-input maxlength="175" type="textarea" v-model="claim.admin_comments"></b-input>
                </b-field>

                <button class="button is-success" @click="approve(claim)" >Approve This Claim</button>
                <button class="button is-danger" @click="reject(claim)">Deny This Claim</button>
            </div>
            <hr>
        </div>
    </main>
</template>
<script>
    export default {

        data() {
            return {
                ownership_claims:null,
            }
        },
        methods:{
            load()
            {
                this.$root.loading=true;
                axios.get('/webAPI/claims')
                    .then((response)=>{
                        this.$root.loading=false;
                        this.ownership_claims = response.data.ownership_claims;
                    })
                    .catch((error)=>{
                        this.$root.loading=false;
                        this.$root.showNotification('Could not load ownership claims.','danger');
                    })
            },
            approve(claim)
            {
                if(confirm("Are you sure you want to give ownership of "+claim.place.name+" to "+claim.user.name+"?"))
                {
                    claim.is_approved=1;
                    claim.is_rejected=0;
                    this.makeAPICall(claim);
                }
            },
            deny(claim)
            {
                if(confirm("Are you sure you want to reject "+claim.user.name+"'s claim of ownership for "+claim.place.name+"?"))
                {
                    claim.is_approved=1;
                    claim.is_rejected=0;
                    this.makeAPICall(claim);
                }
            },
            makeAPICall(claim)
            {
                this.$root.loading=true;
                axios.post('/webAPI/claims',claim)
                    .then((response)=>{
                        this.$root.loading=false;
                        this.$root.showNotification('Processed ownership claim for'+claim.place.name+'.','success');
                    })
                    .catch((error)=>{
                        this.$root.loading=false;
                        this.$root.showNotification('Could process ownership claim.','danger');
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