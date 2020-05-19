// Requires JQuery

(function($) {
    const headers = ['Student Resources'];

    const items = [
        'Production Auditions',
        'Music Ensembles',
        'Scholarships',
        'Course Listings',
        'Student Organizations',
        'Production Calendar',
        'Production Archives',
    ]

    const nameCheck = function(context, name) {
        return $(context).is(':contains("' + name + '")')
    }

    $(document).ready(function() {
        $('#header-menu').find('.dropdown-item').each(function() {

            headers.forEach(item => {
                if (nameCheck(this, item)) {
                    $(this).addClass('spasubmenu')
                }
            })

            items.forEach(item => {
                if (nameCheck(this, item)) {
                    $(this).addClass('small')
                }
            })
        })
    })
    
})(jQuery)