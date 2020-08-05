/* eslint no-undef: 1 */

import Vue from 'vue'
import axios from 'axios'

import StudentInfo from '../StudentInfo'
import ProgramSelect from '../ProgramSelect'
import AuditionInfo from '../AuditionInfo'
import ReCaptchaBox from '../ReCaptchaBox'

import {EventBus} from '../event-bus'

export default {
    components: {
        'student-info': StudentInfo,
        'program-select': ProgramSelect,
        'audition-info': AuditionInfo,
        'recaptcha': ReCaptchaBox,
    },
    data() {
        return{
            values: {
                studentInfo: {
                    fname: '',
                    lname: '',
                    email: '',
                    pid: '',
                    phone: '',
                    address: {
                        street1: '',
                        street2: '',
                        city: '',
                        state: '',
                        zip: '',
                    },
                    parentName: '',
                    preferredName: '',
                    pronouns: '',
                    pronounOther: '',
                },
                level: '',
                program: {
                    label: '',
                    name: '',
                },
                year: '',
                hsName: '',
                hsCounty: '',
                instrument: '',
                instrumentYears: '',
                date: '',
            },
            buttons: {
                level: [
                    {
                        text: 'Undergraduate',
                        value: 'undergrad',
                        type: 'radio',
                    },
                    {
                        text: 'Graduate',
                        value: 'grad',
                        type: 'radio',
                    },
                ],
                programs: {
                    undergrad: [
                        {
                            text: 'Bachelor of Arts, Music',
                            value: 'ba-music',
                        },
                        {
                            text: 'Bachelor of Music',
                            value: 'bm-',
                        },
                        {
                            text: 'Bachelor of Music Education',
                            value: 'bme',
                        },
                    ],
                    grad: [
                        {
                            text: 'Master of Arts, Music',
                            value: 'ma-music',
                        },
                    ],
                    tracks: [
                        {
                            text: 'Performance Track',
                            value: 'performance',
                        },
                        {
                            text: 'Jazz Studies Track',
                            value: 'jazz',
                        },
                        {
                            text: 'Composition Track',
                            value: 'composition',
                        },
                    ],
                },
                years: [
                    {
                        text: "Freshman",
                        value: "freshman",
                    },
                    {
                        text: "Sophomore",
                        value: "sophomore",
                    },
                    {
                        text: "Junior",
                        value: "junior",
                    },
                    {
                        text: "Senior",
                        value: "senior",
                    },
                ],
            },
            fields: {
                studentInfo: [
                    {
                        label: 'First Name',
                        name: 'fname',
                        colWidth: 6,
                        required: true,
                    },
                    {
                        label: 'Last Name',
                        name: 'lname',
                        colWidth: 6,
                        required: true,
                    },
                    {
                        label: 'Email',
                        name: 'email',
                        colWidth: 6,
                        type: 'email',
                        required: true,
                    },
                    {
                        label: 'UCF ID (PID)',
                        name: 'pid',
                        colWidth: 6,
                        type: 'number',
                        required: true,
                        maxLength: 7
                    },
                    {
                        label: 'Phone',
                        name: 'phone',
                        colWidth: 6,
                        type: 'tel',
                        maxLength: 10,
                        required: true,
                    },
                    {
                        name: 'address',
                        fields: [
                            {
                                label: 'Street Address (Line 1)',
                                name: 'street1',
                                colWidth: 6,
                                required: true,
                            },
                            {
                                label: 'Street Address (Line 2)',
                                name: 'street2',
                                colWidth: 6,
                            },
                            {
                                label: 'City',
                                name: 'city',
                                colWidth: 5,
                                required: true,
                            },
                            {
                                label: 'State',
                                name: 'state',
                                colWidth: 3,
                                maxLength: 2,
                                required: true,
                            },
                            {
                                label: 'ZIP Code',
                                name: 'zip',
                                type: 'number',
                                colWidth: 4,
                                maxLength: 5,
                                required: true,
                            }
                        ],
                    },
                    {
                        label: "Parent or Guardian Name (if under 18)",
                        name: 'parentName',
                        colWidth: 6,
                    },
                    {
                        label: "Chosen or Preferred Name",
                        name: 'preferredName',
                        colWidth: 6,
                    }
                ],
                auditionInfo: [
                    {
                        label: "High School",
                        name: "hsName",
                        colWidth: 6,
                        required: true,
                    },
                    {
                        label: "County",
                        name: "hsCounty",
                        colWidth: 6,
                        required: true,
                    }
                ]
            },
            baseUrl: wpVars.baseUrl,
            ajaxUrl: wpVars.ajaxUrl,
            wpNonce: '',
            rcSiteKey: wpVars.reCAPTCHA,
            lang: wpVars.lang,
            isSubmitted: false,
            isProcessing: false,
            serverResponse: '',
        }
    },
    computed: {
        showAuditionInfo() {
            return this.values.level === 'undergrad'
                && this.values.program.name !== ''
                && this.values.program.name.substring(0, 3) !== 'ma-'
                && (this.values.program.name.length > 3
                        || this.values.program.name === 'bme')
        },
        displayMessage() {
            if (this.isSubmitted && !this.isProcessing) {
                return this.serverResponse
            }
            else {
                return "Processing..."
            }
        },
    },
    methods: {
        submitForm(event) {
            event.preventDefault()
            event.stopImmediatePropagation()
            
            document.querySelector('#submitButton').disabled = true
            this.isProcessing = true
            this.isSubmitted = true

            const data = {
                firstName: this.values.studentInfo.fname,
                lastName: this.values.studentInfo.lname,
                email: this.values.studentInfo.email,
                pid: this.values.studentInfo.pid,
                phone: this.values.studentInfo.phone,
                parentName: this.values.studentInfo.parentName,
                preferredName: this.values.studentInfo.preferredName,
                pronouns: this.values.studentInfo.pronouns,
                pronounOther: this.values.studentInfo.pronounOther,
                program: this.values.program.name,
                year: this.values.year,
                schoolName: this.values.hsName,
                schoolCounty: this.values.hsCounty,
                instrument: this.values.instrument,
                instrumentYears: this.values.instrumentYears,
                date: this.values.date,
            }

            const address = this.values.studentInfo.address
            data.address = `${address.street1}${address.street2 !== '' ? `, ${address.street2}` : ''}, ${address.city}, ${address.state} ${address.zip}`

            const formData = new FormData

            formData.append('action', 'music_form_submit')
            formData.append('music-form-nonce', this.wpNonce)

            const reCaptcha = document.querySelector(`#g-recaptcha-response`)
            formData.append('g-recaptcha-response', reCaptcha.value)

            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value)
            }

            console.log(formData.entries())
            
            axios.post(this.ajaxUrl, formData)
                .then(response => {this.serverResponse = response.data})
                .catch(error => {console.error(error)})
                .finally(() => {this.isProcessing = false})
        },
        textInputChange({name, value, parent}) {
            if (parent !== undefined && parent !== null) 
                Vue.set(this.values[parent], name, value)
            else
                Vue.set(this.values, name, value)
        },
        updateAddress(address) {
            this.values.studentInfo.address = address
        },
        changeLevel({value}) {
            this.values.level = value
        },
        changeProgram({value}) {
            let currentProgram = {label: '', name: ''}
            for(const level of ['undergrad', 'grad']) {
                for (const program of this.buttons.programs[level]) {
                    if (program.value === value) {
                        currentProgram.label = program.text
                        currentProgram.name = program.value
                        break
                    }
                }
                if (currentProgram.name !== '')
                    break
            }

            this.values.program = currentProgram
        },
        changeTrack({value}) {

            if (value === 'jazz') {
                this.discipline = 'jazz'
            }
            else {
                this.discipline = 'classical'
            }

            const program = this.values.program
            if (program.name !== '' 
                && program.name.length >= 3 
                && program.name.substring(0, 3) === 'bm-') 
            {
                program.name = `bm-${value}`
            }

            this.values.program = program
        },
        changeYear({value}) {
            this.values.year = value
        },
        changeInstrument({value}) {
            this.values.instrument = value
        },
        changeDate({value}) {
            this.values.date = value
        },
        changePronouns({value}) {
            this.values.studentInfo.pronouns = value
        }
    },
    created() {
        const nonceField = document.querySelector('input[name=music-form-nonce')
        this.wpNonce = nonceField.value
        nonceField.remove()
    },
    mounted() {
        EventBus.$on('textInputChange', this.textInputChange)
        EventBus.$on('addressUpdate', this.updateAddress)
        EventBus.$on('levelChange', this.changeLevel)
        EventBus.$on('programChange', this.changeProgram)
        EventBus.$on('trackChange', this.changeTrack)
        EventBus.$on('instrumentChange', this.changeInstrument)
        EventBus.$on('dateChange', this.changeDate)
        EventBus.$on('yearChange', this.changeYear)
        EventBus.$on('pronounChange', this.changePronouns)
    }
}