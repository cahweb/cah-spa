import Vue from 'vue'
import TextInput from '../TextInput'
import { EventBus } from '../event-bus'

export default {
    components: {
        'text-input': TextInput,
    },
    props: {
        fields: Array,
        totalWidth: Number,
    },
    data() {
        return{
            address: {
                street1: '',
                street2: '',
                city: '',
                state: '',
                zip: '',
            },
        }
    },
    methods: {
        updateAddress({name, value}) {
            Vue.set(this.address, name, value)

            EventBus.$emit('addressUpdate', this.address)
        }
    },
    mounted() {
        EventBus.$on('addressChange', this.updateAddress)
    }
}