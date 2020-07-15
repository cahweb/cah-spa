import {EventBus} from '../event-bus'

export default {
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
            baseUrl: '',
            ajaxUrl: '',
            wpNonce: '',
        }
    },
}