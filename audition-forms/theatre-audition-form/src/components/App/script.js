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
                    lastSchool: '',
                    preferredName: '',
                    pronouns: '',
                    pronounOther: '',
                },
                auditionDates: {
                    first: '',
                    second: '',
                },
                auditionIsZoom: false,
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
                            name: "Kristina Tollefson",
                            email: "kristina.tollefson@ucf.edu",
                        },
                        {
                            label: "B.F.A. Acting",
                            value: "bfa-acting",
                            name: "Be Boyd",
                            email: "belinda.boyd@ucf.edu",
                        },
                        {
                            label: "B.F.A. Design & Technology",
                            value: "bfa-design-tech",
                            name: "Bert Scott",
                            email: "bert.scott@ucf.edu",
                        },
                        {
                            label: "B.F.A. Musical Theatre",
                            value: "bfa-musical-theatre",
                            name: "Earl D. Weaver",
                            email: "earl.weaver@ucf.edu",
                        },
                        {
                            label: "B.F.A. Stage Management",
                            value: "bfa-stage-mgmt",
                            name: "Claudia Lynch",
                            email: "claudia.lynch@ucf.edu",
                        },
                    ],
                    grad: [
                        {
                            label: "M.A. Theatre Studies",
                            value: "ma-theatre",
                            name: "Julia Listengarten",
                            email: "julia.listengarten@ucf.edu",
                        },
                        {
                            label: "M.A. Theatre, Musical Theatre Concentration",
                            value: "ma-music-theatre",
                            name: "Earl D. Weaver",
                            email: "earl.weaver@ucf.edu",
                        },
                        {
                            label: "M.F.A. Acting",
                            value: "mfa-acting",
                            name: "Michael Wainstein",
                            email: "michael.wainstein@ucf.edu",
                        },
                        {
                            label: "M.F.A. Theatre for Young Audiences",
                            value: "mfa-young-theatre",
                            name: "Vandy Wood",
                            email: "vandy.wood@ucf.edu",
                        },
                        {
                            label: "M.F.A. Theatre, Themed Experience",
                            value: "mfa-themed-exp",
                            name: "Peter Weishar",
                            email: "peter.weishar@ucf.edu",
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
                    {
                        label: 'Last School Attended',
                        name: 'lastSchool',
                        required: true,
                    },
                    {
                        label: 'Preferred Name',
                        name: 'preferredName',
                    },
                    {
                        label: 'Pronouns',
                        name: 'pronouns',
                        options: [
                            {
                                text: 'He/Him/His',
                                value: 'he-him-his',
                            },
                            {
                                text: 'She/Her/Hers',
                                value: 'she-her-hers',
                            },
                            {
                                text: 'They/Them/Theirs',
                                value: 'they-them-theirs',
                            },
                            {
                                text: 'Other',
                                value: 'other'
                            }
                        ],
                    }
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
                undergrad: [],
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
            maxUploadSize: 10485760, // 10 MB in bytes
            rcSiteKey: wpVars.reCAPTCHA,
            rcLang: wpVars.lang,
        }
    },
    computed: {
        // The program the user has selected
        selectedProgram() {
            return this.values.program !== '' ? this.values.program.value : null
        },
        // Whether to show program requirements.
        showReqs() {
            if (this.values.level !== '' && this.values.program !== '') {
                return this.requirementList[this.values.level][this.values.program.value] !== undefined
            }
            return false
        },
        // Get the correct program requirements
        programReqs() {
            if (this.values.level !== '' && this.values.program !== '') {
                const reqArray = this.requirementList[this.values.level][this.values.program.value]
                if (reqArray !== undefined && reqArray.length > 0) {
                    return reqArray.join("\n")
                }
            }
            return "Not found"
        },
        // Check whether the user has selected the BFA Acting program
        isBfaActing() {
            return this.values.level === 'undergrad' && (this.values.program !== '' && this.values.program.value === 'bfa-acting')
        },
        // Whether to show the form part of the app.
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
        // Get the right list of possible audition dates.
        dateList() {
            if (this.selectedProgram === 'ba-theatre')
                return this.dateOptions[this.selectedProgram]

            return this.dateOptions.general
        },
        // Check if both dates are the same.
        sameDate() {
            return (this.values.auditionDates.first !== '' && this.values.auditionDates.second !== '') 
                && this.values.auditionDates.first == this.values.auditionDates.second
        },
        // This could technically be in data(), but I guess I wanted the option
        // to do some programmatic decision-making, if necessary.
        acceptedFileTypes() {
            return "application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/rtf, image/jpeg, image/png, text/plain"
        },
        // Shorter way to access the extra files
        extraFileList() {
            return this.values.files.extra
        },
        // Whether to show the Acceptd message
        showAcceptd() {
            return this.values.program !== '' 
                && ((this.isBfaActing && this.values.actingChoice === 'video') 
                    || (this.acceptdPrograms[this.values.level] !== undefined 
                        && this.acceptdPrograms[this.values.level].includes(this.values.program.value)))
        },
        // The Acceptd message
        acceptdMessage() {
            return `Click the link to continue to <a href="https://app.getacceptd.com/ucftheatre" target="_blank" rel="noopener">Acceptd</a> in order to complete your application.`
        },
        // Whether the files are still uploading
        isUploading() {
            return this.isProcessing && this.uploadPercentage < 100
        },
        // Whether the back-end is still scanning the files
        isScanning() {
            return this.isProcessing && this.uploadPercentage === 100
        },
        // The status message to display
        statusMessage() {
            if (this.isUploading) {
                return "Uploading files..."
            }
            else if (this.isScanning) {
                return "Scanning submitted files for viruses..."
            }
            else 
                return this.doneMessage
        },
        totalFileSize() {
            const resumeSize = this.values.files.resume.size
            const extraFilesSize = this.values.files.extra.length
                ? this.values.files.extra.reduce((totalSize, extraFile) => totalSize + (extraFile !== null ? extraFile.size : 0))
                : 0
            return resumeSize + extraFilesSize
        },
    },
    methods: {
        /**
         * Gives the currently selected button the .active CSS class
         * 
         * @param {String} name  The name of the input in question
         */
        updateActive(name) {
            // Store all buttons with the same name
            const buttons = document.querySelectorAll(`input[name=${name}]`)

            // Loop through and only give the .active class to the checked one
            buttons.forEach(item => {
                if (item.checked)
                    item.closest('label').classList.add('active')
                else
                    item.closest('label').classList.remove('active')
            })
        },
        /**
         * Updates data() to store the correct File objects.
         * 
         * @param {String} name  The name of the file input field
         * @param {Number} index The index of the field, if it's not the resume
         * @param {File} file  The File object in question
         */
        fileChange({name, index, file}) {
            // If it's the resume, just update it
            if ('resume' === name)
                this.values.files.resume = file
            
            // Otherwise update the extras array in a way that Vue will
            // still react to
            else {
                const newArray = this.values.files.extra.map((item, i) => i === index ? file : item)
                this.values.files.extra = newArray
            }
        },
        /**
         * Removes a file from the form.
         * 
         * @param {String} name  The name of the file input field
         * @param {Number} index  The index of the field, if not the resume
         */
        removeFile({name, index}) {

            // If it's the resume, just make it null
            if ('resume' === name) {
                this.values.files.resume = null
            }
            // Otherwise, update the extras array in a way that Vue will still
            // react to
            else {
                const newArray = this.values.files.extra.filter((item, i) => i !== index)
                this.values.files.extra = newArray
            }
        },
        /**
         * Add an empty entry to the extra file array
         */
        newFile() {
            if (this.extraFileList.length < 9 && this.totalFileSize < this.maxUploadSize)
                this.values.files.extra.push(null)
            else
                alert("Maximum of 10 files allowed, for a maximum total of 10 MB.")
        },
        /**
         * Executes when the user clicks "Submit"
         * 
         * @param {Event} event  The 'click' or 'submit' event
         */
        submitForm(event) {
            // Stop the normal event response
            event.preventDefault()
            event.stopImmediatePropagation()

            // Set the booleans for the View updates
            this.isSubmitted = true
            this.isProcessing = true

            // We'll flatten the data structure here. Yes, I could have done
            // this from the beginning, but tracking the data the other way in
            // the app made more sense to me. So there.
            const studentInfo = this.values.studentInfo
            const address = studentInfo.address

            const auditionDates = this.values.auditionDates

            for (const [index, value] of Object.entries(auditionDates)) {
                auditionDates[index] = 'next-available' === value ? '1970-01-01' : value
            }

            const data = {
                lname: studentInfo.lname,
                fname: studentInfo.fname,
                email: studentInfo.email,
                address: `${address.street1},${address.street2 != '' ? ' ' + address.street2 + ',' : ''} ${address.city}, ${address.state} ${address.zip}`,
                phone: studentInfo.phone,
                preferredName: studentInfo.preferredName,
                pronouns: studentInfo.pronouns,
                pronounOther: studentInfo.pronounOther,
                lastSchool: studentInfo.lastSchool,
                level: this.values.level,
                program: this.values.program.value,
                firstChoiceDate: auditionDates.first,
                secondChoiceDate: auditionDates.second,
                auditionisZoom: this.values.auditionisZoom ? 1 : 0,
                resume: this.values.files.resume,
            }

            // Flatten any extra files and stick them in data
            for (const [index, file] of this.values.files.extra.entries()) {
                data[`extra${index}`] = file
            }

            // Create a new FormData object
            const formData = new FormData()

            // Add our action and nonce, for the WP back-end
            formData.append('action', 'theatre_form_submit')
            formData.append('theatre-form-nonce', this.nonce)

            // Add all the elements of data to our FormData object
            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value)
            }

            // For debug
            /*
            const testObj = {}
            for (const [key, value] of formData.entries()) {
                testObj[key] = value
            }
            console.log(testObj)
            */

            // Set the options for Axios. We're just handling upload progress
            // updates, so we can keep our progress bar current
            const options = {
                onUploadProgress: event => {this.uploadPercentage = parseInt(Math.round((event.loaded / event.total) * 100))},
            }

            // Post the response. I used to think that, for WordPress, you had
            // to do all this janky wizardry to keep it from sending the data
            // as JSON, but you can just send a FormData object instead and
            // WordPress will understand it just fine
            axios.post(this.ajaxUrl, formData, options)
                .then(response => {
                    // Set the status message to the server response
                    this.doneMessage = response.data
                })
                // Log any errors
                .catch(err => {console.error(err)})
                // Finish processing
                .finally(() => {this.isProcessing = false})
        },
    },
    // Executes when the App is first created, before anything has
    // been rendered
    created() {
        // Get our program requirements
        let url = `${this.baseUrl}/dist/json/theatre-program-reqs.json`
        axios.get(url).then(response => {
            this.requirementList = response.data
        })

        // Get our date options
        url = `${this.baseUrl}/dist/json/theatre-audition-dates.json`
        axios.get(url).then(response => {
            this.dateOptions = response.data
        })

        // Get our nonce value and ditch the input
        const nonceField = document.querySelector('input[name=theatre-form-nonce]')
        this.nonce = nonceField.value
        nonceField.remove()
    },
    mounted() {
        const recaptchaDiv = document.querySelector('#recaptcha-div')
        const rcScript = document.createElement('script')
        rcScript.setAttribute('src', `https://www.google.com/recaptcha/api.js?h1=${this.rcLang}`)

        recaptchaDiv.append(rcScript)
    }
}