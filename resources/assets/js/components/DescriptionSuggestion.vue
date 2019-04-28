<template>
    <b-modal :active.sync="descriptionSuggestion.show" :width="640">
        <div class="modal-background"></div>
        <div class="modal-card"  >
            <header class="modal-card-head">
                <h2 class="modal-card-title">Suggest a Description</h2>
            </header>
            <section class="modal-card-body has-text-white">
                <label class="heading has-text-white" for="description-suggestion">Suggest a Description for <strong class="has-text-weight-bold has-text-white">@{{descriptionSuggestion.name}}</strong></label>
                <textarea id="description-suggestion" class="textarea" v-model="descriptionSuggestion.description" :placeholder="'How would you describe \''+descriptionSuggestion.name+'\'?'">
                        </textarea>

            </section>
            <footer class="modal-card-foot">
                <button @click="submitDescriptionSuggestion" class="button is-small is-success">Submit Suggestion</button>
                <button class="button is-danger is-small" @click="descriptionSuggestion.show=false">Cancel</button>
            </footer>
        </div>
        <button class="modal-close is-large" aria-label="close" @click="descriptionSuggestion.show=false"></button>
    </b-modal>
</template>
<script>
    export default{
        data(){
            return {
                descriptionSuggestion: this.$root.descriptionSuggestion
            };
        },
        methods:{
            /**
             * let's the user make a suggestion for a place that does not have a description yet
             * this method is used by Place.vue, Listing.vue, and Home.vue
             * basically anywhere a place description is displayed.
             * This function will make the actual API call to our server to save the suggestion and toggle the modal and notification messages
             */
            submitDescriptionSuggestion()
            {
                this.loading=true;
                axios.post('/api/places/description',{
                    id:this.descriptionSuggestion.id,
                    description:this.descriptionSuggestion.description,
                })
                    .then((response) => {
                        this.loading = false;

                    })
                    .catch((error) => {
                        this.loading = false;

                    });

                /*always do this, we really don't care about the response from the server as the user shouldn't have to be
                worried about it--and there is nothing they can do if it fails, either.
                 */
                this.descriptionSuggestion={
                    show:false,
                    name:null,
                    id:null,
                    description:null
                };
                this.$root.showNotification('Thanks, we appreciate people like you! We have received your suggestion and will review its suitability for use on EatLocalICT! ','success')
            },
        }

    }
</script>