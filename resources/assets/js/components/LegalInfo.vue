<template>
    <b-modal :active.sync="$root.showLegalInfo" @onCancel="$root.toggleLegalInfoModal(false)">
        <div class="modal-background"></div>
        <div class="modal-card">
            <div class="modal-card-body has-text-white" v-html="body">
            </div>
        </div>
    </b-modal>
</template>
<script>
    export default {
        props: ['type'],
        data() {
            return {
                body:null,
                loading:false
            }//return
        },//data
        methods:{
            changeType(){
                this.body='';
                let url = '';
                switch(this.type)
                {
                    case 'terms':
                        url = '/legal/terms.html';
                        break;
                    case 'cookies':
                        url = '/legal/cookies.html';
                        break;
                    default:
                        return;
                }
                this.$root.loading=true;
                axios.get(url)
                    .then((response) => {
                        this.body = response.data;
                        this.$root.loading=false;
                    })
                    .catch((error) => {
                        this.$root.loading=false;
                    });
            }
        },
        mounted(){

         this.changeType();
        },
        watch: {
            type: function() { // watch it
                this.changeType();
            }
        }
    }//export
</script>