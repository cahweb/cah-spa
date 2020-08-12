import {EventBus} from '../event-bus'

export default {
    props: {
        buttonList: {
            type: Array,
            required: true,
        },
        name: String,
        inline: {
            type: Boolean,
            default: false,
        },
        emitOn: {
            type: String,
            required: true,
        },
        defaultType: {
            type: String,
            default: 'radio',
        }
    },
    data() {
        return{
            value: '',
        }
    },
    methods: {
        buttonClick(e) {
            this.setActive(e.target)

            EventBus.$emit(this.emitOn, {name: this.name, value: this.value})
        },
        setActive(target) {
            const name = target.getAttribute('name')

            const buttons = document.querySelectorAll(`input[name=${name}]`)

            if (buttons !== undefined) {
                buttons.forEach(item => {
                    const label = item.parentNode
                    if (item.getAttribute('value') === this.value)
                        label.classList.add('active')
                    else
                        label.classList.remove('active')
                })
            }
        },
    }
}