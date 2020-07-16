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
        return{}
    },
    computed: {
        address() {
            output = {}
            for (const [key, value] )
        }
    },
    methods: {
        updateAddress({name, value}) {
            Vue.set(this, name, value)
        }
    },
    mounted() {
        EventBus.$on('addressChange', this.updateAddress)
    }
}