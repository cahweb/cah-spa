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
        // Shortcut to access the input's FileList
        files() {
            return this.$refs.input.files
        }
    },
    methods: {
        /**
         * Handle updates to the file input and fire an event to update
         * the Model's state
         */
        fileChange() {
            // If we've got a file, send it on up to the App
            if (this.files.length > 0) {
                this.$emit('fileChange', { name: this.name, index: this.index, file: this.files[0]})
            }
            // Otherwise treat it like removing one
            else {
                this.removeFile()
            }
        },
        /**
         * Removes a file from the file input and fires an event to
         * update the Model
         * 
         * @param {Event} e 
         */
        removeFile(e) {
            // Stop the normal click handling
            e.preventDefault()

            // If there's a file in the input, empty it out
            if (this.files.length > 0) {
                // So FileList objects aren't writeable, and you can't
                // even create blank one, but you can create a DataTransfer
                // object, which has a files attribute that is, by default,
                // an empty FileList. Janky AF, but it's the only way I
                // could find to do it.
                const emptyList = new DataTransfer()
                
                // Assign that empty FileList to the input
                this.$refs.input.files = emptyList.files
            }
            
            // Emit the event and update the App model.
            this.$emit('removeFile', {name: this.name, index: this.index})
        }
    },
    // Fires when the component is first attached to the App instance, but
    // before it starts listening for updates
    mounted() {
        // If we have a File object already, we'll use the same trick as
        // removeFile(), above, to add it to the input at start.
        if (this.file instanceof File) {
            // Create a DataTransfer object and add the File object to its
            // list of items. Under the hood, this will magically create a
            // new FileList
            const fileList = new DataTransfer()
            fileList.items.add(this.file)

            // Assign that FileList to the input
            this.$refs.input.files = fileList.files
        }
    }
}