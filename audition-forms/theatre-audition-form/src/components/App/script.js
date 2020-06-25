/* eslint no-undef: 1 */

import axios from 'axios'

import FileInput from '../FileInput'
import ProgressBar from '../ProgressBar'

export default {
    components: {
        "file-input": FileInput,
        "progress-bar": ProgressBar,
    },
    data() {
        return{
            values: {
                level: '',
                program: '',
                actingChoice: '',
                studentInfo: {
                    fname: '',
                    lname: '',
                    email: '',
                    phone: '',
                    address: {
                        street1: '',
                        street2: '',
                        city: '',
                        state: '',
                        zip: '',
                    },
                },
                auditionDates: {
                    first: '',
                    second: '',
                },
                files: {
                    resume: null,
                    extra: [],
                },
            },
            buttons: {
                level: [
                    {
                        label: "Undergraduate",
                        value: "undergrad",
                    },
                    {
                        label: "Graduate",
                        value: "grad",
                    },
                ],
                programs: {
                    undergrad: [
                        {
                            label: "B.A. Theatre Studies",
                            value: "ba-theatre",
                        },
                        {
                            label: "B.F.A. Acting",
                            value: "bfa-acting",
                        },
                        {
                            label: "B.F.A. Design & Technology",
                            value: "bfa-design-tech",
                        },
                        {
                            label: "B.F.A. Musical Theatre",
                            value: "bfa-musical-theatre",
                        },
                        {
                            label: "B.F.A. Stage Management",
                            value: "bfa-stage-mgmt",
                        },
                    ],
                    grad: [
                        {
                            label: "M.A. Theatre Studies",
                            value: "ma-theatre",
                        },
                        {
                            label: "M.A. Theatre, Musical Theatre Concentration",
                            value: "ma-music-theatre",
                        },
                        {
                            label: "M.F.A. Acting",
                            value: "mfa-acting",
                        },
                        {
                            label: "M.F.A. Theatre for Young Audiences",
                            value: "mfa-young-theatre",
                        },
                        {
                            label: "M.F.A. Theatre, Themed Experience",
                            value: "mfa-themed-exp",
                        },
                    ],
                },
                bfaActingChoice: [
                    {
                        label: "Live Audition",
                        value: "live",
                    },
                    {
                        label: "Submit Video",
                        value: "video",
                    },
                ],
            },
            inputs: {
                studentInfo: [
                    {
                        label: 'First Name',
                        name: 'fname',
                        required: true,
                    },
                    {
                        label: 'Last Name',
                        name: 'lname',
                        required: true,
                    },
                    {
                        label: 'Email',
                        name: 'email',
                        type: 'email',
                        required: true,
                    },
                    {
                        label: 'Phone',
                        name: 'phone',
                        type: 'tel',
                        maxlength: 10,
                        required: true,
                    },
                ],
                address: [
                    {
                        label: 'Street Address (Line 1)',
                        name: 'street1',
                        required: true,
                    },
                    {
                        label: 'Street Address (Line 2)',
                        name: 'street2',
                        required: false,
                    },
                    {
                        label: 'City',
                        name: 'city',
                        required: true,
                    },
                    {
                        label: 'State',
                        name: 'state',
                        colWidth: 2,
                        maxlength: 2,
                        required: true,
                    },
                    {
                        label: 'ZIP Code',
                        name: 'zip',
                        type: 'number',
                        colWidth: 4,
                        maxlength: 5,
                        required: true,
                    },
                ],
            },
            acceptdPrograms: {
                undergrad: [
                    'bfa-musical-theatre',
                ],
                grad: [
                    'mfa-young-theatre',
                ],
            },
            requirementList: {},
            dateOptions: {},
            baseUrl: wpVars.baseUrl,
            ajaxUrl: wpVars.ajaxUrl,
            nonce: '',
            isSubmitted: false,
            isProcessing: false,
            uploadPercentage: 0,
            doneMessage: '',
        }
    },
    computed: {
        selectedProgram() {
            return this.values.program !== '' ? this.values.program.value : null
        },
        showReqs() {
            if (this.values.level !== '' && this.values.program !== '') {
                return this.requirementList[this.values.level][this.values.program.value] !== undefined
            }
            return false
        },
        programReqs() {
            if (this.values.level !== '' && this.values.program !== '') {
                const reqArray = this.requirementList[this.values.level][this.values.program.value]
                if (reqArray !== undefined && reqArray.length > 0) {
                    return reqArray.join("\n")
                }
            }
            return "Not found"
        },
        isBfaActing() {
            return this.values.level === 'undergrad' && (this.values.program !== '' && this.values.program.value === 'bfa-acting')
        },
        showForm() {
            const program = this.selectedProgram
            if (this.values.level === 'undergrad'
                && (program === 'ba-theatre'
                    || program === 'bfa-design-tech'
                    || program === 'bfa-stage-mgmt'
                    || (program === 'bfa-acting'
                        && this.values.actingChoice === 'live'
                        )
                    )
                ) {
                return true
            }
            return false
        },
        dateList() {
            if (this.selectedProgram === 'ba-theatre')
                return this.dateOptions[this.selectedProgram]

            return this.dateOptions.general
        },
        sameDate() {
            return (this.values.auditionDates.first !== '' && this.values.auditionDates.second !== '') 
                && this.values.auditionDates.first == this.values.auditionDates.second
        },
        acceptedFileTypes() {
            return "application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/rtf, image/jpeg, image/png, text/plain"
        },
        extraFileList() {
            return this.values.files.extra
        },
        showAcceptd() {
            return this.values.program !== '' 
                && ((this.isBfaActing && this.values.actingChoice === 'video') 
                    || (this.acceptdPrograms[this.values.level] !== undefined 
                        && this.acceptdPrograms[this.values.level].includes(this.values.program.value)))
        },
        acceptdMessage() {
            return `Click the link to continue to <a href="https://app.getacceptd.com/ucftheatre" target="_blank" rel="noopener">Acceptd</a> in order to complete your application.`
        },
        isUploading() {
            return this.isProcessing && this.uploadPercentage < 100
        },
        isScanning() {
            return this.isProcessing && this.uploadPercentage === 100
        },
        statusMessage() {
            if (this.isUploading) {
                return "Uploading files..."
            }
            else if (this.isScanning) {
                return "Scanning submitted files for viruses..."
            }
            else 
                return this.doneMessage
        }
    },
    methods: {
        updateActive(name) {
            const buttons = document.querySelectorAll(`input[name=${name}]`)
            buttons.forEach(item => {
                if (item.checked)
                    item.closest('label').classList.add('active')
                else
                    item.closest('label').classList.remove('active')
            })
        },
        fileChange({name, index, file}) {
            if ('resume' === name)
                this.values.files.resume = file
            else {
                const newArray = this.values.files.extra.map((item, i) => i === index ? file : item)
                this.values.files.extra = newArray
            }
        },
        removeFile({name, index}) {
            if ('resume' === name) {
                this.values.files.resume = null
            }
            else {
                const newArray = this.values.files.extra.filter((item, i) => i !== index)
                this.values.files.extra = newArray
            }
        },
        newFile() {
            this.values.files.extra.push(null)
        },
        submitForm(event) {
            event.preventDefault()
            event.stopImmediatePropagation()

            this.isSubmitted = true
            this.isProcessing = true

            // We'll flatten the data structure here. Yes, I could have done this
            // from the beginning, but tracking the data the other way in the app
            // made more sense to me. So there.
            const studentInfo = this.values.studentInfo
            const address = studentInfo.address

            const data = {
                lname: studentInfo.lname,
                fname: studentInfo.fname,
                email: studentInfo.email,
                address: `${address.street1},${address.street2 != '' ? ' ' + address.street2 + ',' : ''} ${address.city}, ${address.state} ${address.zip}`,
                phone: studentInfo.phone,
                level: this.values.level,
                program: this.values.program.value,
                firstChoiceDate: this.values.auditionDates.first,
                secondChoiceDate: this.values.auditionDates.second,
                resume: this.values.files.resume,
            }

            // Flatten any extra files and stick them in data
            for (const [index, file] of this.values.files.extra.entries()) {
                data[`extra${index}`] = file
            }

            const formData = new FormData()

            formData.append('action', 'theatre_form_submit')
            formData.append('theatre-form-nonce', this.nonce)

            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value)
            }

            const testObj = {}
            for (const [key, value] of formData.entries()) {
                testObj[key] = value
            }
            console.log(testObj)

            const options = {
                onUploadProgress: event => {this.uploadPercentage = parseInt(Math.round((event.loaded / event.total) * 100))},
            }

            axios.post(this.ajaxUrl, formData, options)
                .then(response => {
                    this.doneMessage = response.data
                })
                .catch(err => {console.error(err)})
                .finally(() => {this.isProcessing = false})
        },
    },
    created() {
        let url = `${this.baseUrl}/dist/json/theatre-program-reqs.json`
        axios.get(url).then(response => {
            this.requirementList = response.data
        })

        url = `${this.baseUrl}/dist/json/theatre-audition-dates.json`
        axios.get(url).then(response => {
            this.dateOptions = response.data
        })

        const nonceField = document.querySelector('input[name=theatre-form-nonce]')
        this.nonce = nonceField.value
        nonceField.remove()
    }
}