import ButtonGroup from '../ButtonGroup'

import {EventBus} from '../event-bus'

export default {
    components: {
        'button-group': ButtonGroup,
    },
    props: {
        levels: Array,
        programs: Object,
    },
    data() {
        return{
            level: '',
            program: '',
            track: '',
        }
    },
    computed: {
        showTracks() {
            return this.level === 'undergrad' && (this.program !== '' && this.program.substring(0, 3) === 'bm-')
        }
    },
    methods: {
        changeLevel({value}) {
            this.level = value
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
    mounted() {
        EventBus.$on('levelChange', this.changeLevel)
        EventBus.$on('programChange', this.changeProgram)
        EventBus.$on('trackChange', this.changeTrack)
    }
}