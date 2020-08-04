/* eslint no-undef: 1 */

import axios from 'axios'

import TextInput from '../TextInput'
import ButtonGroup from '../ButtonGroup'

import {EventBus} from '../event-bus'

export default {
    components: {
        'text-input': TextInput,
        'button-group': ButtonGroup,
    },
    props: {
        buttonList: {
            type: Array,
            required: true,
        },
        fieldList: {
            type: Array,
            required: true,
        },
    },
    data() {
        return{
            discipline: 'classical',
            instrument: '',
            date: '',
            instrumentList: {},
            dateList: {},
            baseUrl: wpVars.baseUrl,
        }
    },
    computed: {
        availableInstruments() {
            if (this.discipline === 'jazz') {
                return this.instrumentList.jazz
            }
            else {
                return this.instrumentList.classical
            }
        }
    },
    methods: {
        checkJazz({value}) {
            if (value === 'jazz') {
                this.discipline = 'jazz'
                this.instrument = ''
            }
            else {
                this.discipline = 'classical'
                this.instrument = ''
            }
        },
        isBlackedOut(possibleDate) {
            if (this.instrument === '') return false

            for (const instrument of this.instrumentList[this.discipline]) {
                if (this.instrument === instrument.value 
                    && instrument.blackout !== undefined 
                    && instrument.blackout.includes(possibleDate))
                {
                    return true
                }
            }
            return false
        },
        instrumentChange() {
            EventBus.$emit('instrumentChange', {value: this.instrument})
        },
        dateChange() {
            EventBus.$emit('dateChange', {value: this.date})
        }
    },
    created() {
        const url = `${this.baseUrl}/dist/json`

        axios.get(`${url}/music-audition-instruments.json`)
            .then(response => {this.instrumentList = response.data})

        axios.get(`${url}/music-audition-dates.json`)
            .then(response => {this.dateList = response.data})
    },
    mounted() {
        EventBus.$on('trackChange', this.checkJazz)
    }
}