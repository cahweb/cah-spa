// Requires JQuery

(function($) {
   // const headers = ['Student Resources'];

    const items = [
        'Production Auditions',
        'Music Ensembles',
        'Scholarships',
        //'Course Listings',
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

        $(".navbar-brand").css("font-size", "1.1rem");
        $(".navbar-brand").html("School of Performing&nbsp;Arts");

        $("#header-menu>ul").find("li").each(function() {
            $(this).children("a").css("text-align", "center");
            $(this).children("a").css("font-size", "0.7rem");
            $(this).children("a").css("padding-left", "0.75rem");
            $(this).children("a").css("padding-right", "0.75rem");
        })
    })
})(jQuery)