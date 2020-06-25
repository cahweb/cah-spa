export default {
    props: {
        name: String,
        acceptedFileTypes: String,
        index: Number,
        file: File,
        required: Boolean,
    },
    data() {
        return{}
    },
    computed: {
        files() {
            return this.$refs.input.files
        }
    },
    methods: {
        fileChange() {
            if (this.files.length > 0) {
                this.$emit('fileChange', { name: this.name, index: this.index, file: this.files[0]})
            }
            else {
                this.removeFile()
            }
        },
        removeFile(e) {
            e.preventDefault()

            if (this.files.length > 0) {
                const emptyList = new DataTransfer()
                this.$refs.input.files = emptyList.files
            }
            
            this.$emit('removeFile', {name: this.name, index: this.index})
        }
    },
    mounted() {
        if (this.file instanceof File) {
            const fileList = new DataTransfer()
            fileList.items.add(this.file)
            this.$refs.input.files = fileList.files
        }
    }
}