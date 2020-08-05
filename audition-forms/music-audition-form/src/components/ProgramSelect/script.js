import ButtonGroup from '../ButtonGroup'

import {EventBus} from '../event-bus'
import axios from 'axios'

export default {
    components: {
        'button-group': ButtonGroup,
    },
    props: {
        levels: {
            type: Array,
            required: true,
        },
        programs: {
            type: Object,
            required: true,
        },
        baseUrl: {
            type: String,
            required: true,
        },
    },
    data() {
        return{
            level: '',
            program: '',
            track: '',
            requirementList: {},
            labels: {}
        }
    },
    computed: {
        showTracks() {
            return this.level === 'undergrad' && (this.program !== '' && this.program.substring(0, 3) === 'bm-')
        },
        showReqs() {
            if (this.level !== '' && this.program !== '') {
                if (this.program === 'bm-' && this.track === '')
                    return false
                
                return true
            }
            return false
        },
        programName() {
            let name = ''
            if (this.program.substring(0, 3) !== 'bm-')
                name = this.labels[this.level][this.program]
            else {
                name = this.labels[this.level]['bm-'] + ', ' + this.labels['tracks'][this.track]
            }

            if (name !== undefined) {
                return name
            }
            else
                return "[NOT FOUND]"
        },
        programReqs() {
            const reqBase = this.requirementList[this.level].text
            const program = `${this.program === 'bm-' ? `${this.program}${this.track}` : this.program}`

            const reqSpec = this.requirementList[this.level][program]

            const reqArray = reqSpec !== undefined ? [...reqBase, ...reqSpec] : reqBase

            if (reqArray !== undefined) {
                return reqArray.join("\n")
            }
            else
                return "[NO DATA]"
        },
    },
    methods: {
        changeLevel({value}) {
            this.level = value
            EventBus.$emit('programChange', {value: ''})
            const buttons = document.querySelector('input[name=program]')
            if (buttons !== null )
                buttons.forEach(item => {item.checked = false})
        },
        changeProgram({value}) {
            this.program = value
            if (this.program.substring(0, 3) !== 'bm-' && this.track.length > 0) {
                EventBus.$emit('trackChange', {value: ''})
            }
        },
        changeTrack({value}) {
            this.track = value
        }
    },
    created() {
        const url = `${this.baseUrl}/dist/json`
        axios.get(`${url}/music-program-reqs.json`)
            .then(response => {this.requirementList = response.data})
            .catch(err => {console.error(err)})
    },
    mounted() {
        EventBus.$on('levelChange', this.changeLevel)
        EventBus.$on('programChange', this.changeProgram)
        EventBus.$on('trackChange', this.changeTrack)

        const labels = {}
        for (const [level, data] of Object.entries(this.programs)) {
            const list = {}
            for (const program of data) {
                list[program.value] = program.text
            }

            labels[level] = list
        }
        this.labels = labels
    }
}