import TextInput from '../TextInput'
import Address from '../Address'
import { EventBus } from '../event-bus'

export default {
    components: {
        'text-input': TextInput,
        'address-block': Address,
    },
    props: {
        inputs: Array,
    },
    data() {
        return{
            pronouns: '',
        }
    },
    methods: {
        changePronouns() {
            EventBus.$emit('pronounChange', {value: this.pronouns})
        },
    },
}