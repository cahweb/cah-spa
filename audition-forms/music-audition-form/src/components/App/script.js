import Vue from 'vue'

import TextInput from '../TextInput'

import {EventBus} from '../event-bus'

export default {
    components: {
        'text-input': TextInput,
    },
    data() {
        return{
            values: {
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
                    parentName: '',
                },
                level: '',
                program: '',
            },
            programs: {
                undergrad: [
                    {
                        name: '',
                        label: '',
                    }
                ],
            },
            fields: {
                studentInfo: [
                    {
                        label: 'First Name',
                        name: 'fname',
                        colWidth: 6,
                    },
                    {
                        label: 'Last Name',
                        name: 'lname',
                        colWidth: 6,
                    },
                    {
                        label: 'Email',
                        name: 'email',
                        colWidth: 6,
                        type: 'email',
                    },
                    {
                        label: 'Phone',
                        name: 'phone',
                        colWidth: 6,
                        type: 'tel',
                        maxLength: 10,
                    },
                    {
                        name: 'address',
                        fields: [
                            {
                                label: 'Street Address (Line 1)',
                                name: 'street1',
                                colWidth: 6,
                            },
                            {
                                label: 'Street Address (Line 2)',
                                name: 'street2',
                                colWidth: 6,
                            },
                            {
                                label: 'City',
                                name: 'city',
                                colWidth: 5
                            },
                            {
                                label: 'State',
                                name: 'state',
                                colWidth: 3,
                                maxLength: 2
                            },
                            {
                                label: 'ZIP Code',
                                name: 'zip',
                                type: 'number',
                                colWidth: 4,
                                maxLength: 5,
                            }
                        ],
                    },
                    {
                        label: "Parent or Guardian Name (if under 18)",
                        name: 'parentName',
                        colWidth: 6,
                    }
                ]
            },
            baseUrl: '',
            ajaxUrl: '',
            wpNonce: '',
        }
    },
    methods: {
        textInputChange({name, value, parent}) {
            if (parent !== null) 
                Vue.set(this.values[parent], name, value)
            else
                Vue.set(this.values, name, value)
        },
    },
    mounted() {
        EventBus.$on('textInputChange', this.textInputChange)
    }
}