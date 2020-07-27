import {EventBus} from '../event-bus'

export default {
    props: {
        colWidth: {
            type: Number,
            default: 6,
        },
        type: String,
        label: String,
        name: String,
        val: String,
        maxLength: Number,
        valueParent: String,
        emitOn: String,
        isRequired: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return{
            value: '',
        }
    },
    methods: {
        fieldChange() {
            EventBus.$emit(this.emitOn, {name: this.name, value: this.value, parent: this.valueParent})
        }
    },
    created() {
        if (this.val !== undefined)
            this.value = this.val
    },
}