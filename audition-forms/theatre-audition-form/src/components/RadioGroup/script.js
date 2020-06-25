export default {
    props: {
        buttons: Array,
        inline: Boolean,
        name: String,
    },
    data() {
        return{
            value: '',
        }
    },
    methods: {
        update() {
            const buttons = document.querySelectorAll(`input[name=${this.name}]`)
            buttons.forEach(item => {
                if (item.checked)
                    item.closest('label').classList.add('active')
                else
                    item.closest('label').classList.remove('active')
            })

            this.$emit('radioUpdate', {name: this.name, value: this.value})
        }
    }
}