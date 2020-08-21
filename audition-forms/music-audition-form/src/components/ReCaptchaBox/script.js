export default {
    props: {
        siteKey: {
            type: String,
            required: true,
        },
        lang: {
            type: String,
            required: true,
        },
    },
    data() {
        return{}
    },
    mounted() {
        const recaptchaDiv = document.querySelector('#recaptcha-div')
        const rcScript = document.createElement('script')
        rcScript.setAttribute('src', `https://www.google.com/recaptcha/api.js?h1=${this.lang}`)

        recaptchaDiv.append(rcScript)
    }
}