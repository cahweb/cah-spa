import {EventBus} from '../event-bus'

export default {
    props: {
        colWidth: Number,
        type: String,
        label: String,
        name: String,
        val: String,
        maxLength: Number,
        valueParent: String,
        emitOn: String,
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