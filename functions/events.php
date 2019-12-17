<?
/*
    Upcoming Events
*/

// Shortcode used in Wordpress is the first parameter.
add_shortcode("events", "display_events");

function display_events($atts = [], $content = null) {
    // Attributes given in the shortcode call in Wordpress
    $attributes = shortcode_atts([
        "number-of-events-to-show-per-page" => 5,
        "format" => 0,
    ], $atts);

    $num_events_to_show = $attributes["number-of-events-to-show-per-page"];
    // $format = $attributes["format"];
    $format = 0;

    // Flag for no events in a month.
    global $isEmpty;
    $isEmpty = FALSE;

    /*
        Format is given by the Wordpress shortcode attribute "format".

        0 - (Default) Item list side bar
        1 - Drop down menu

        NOTE: Could probably merge this with all of the event child functions, but that's for future you or me. DRY means nothing to me lol.
    */
    switch ($format) {
        case 1:
            ?>
            <div class="d-flex flex-column">
                <div class="col-sm-9 mx-auto">
                    <? // Filters ?>
                    <section class="col-sm-3 my-5">
                        <?
                            filter_handler($format)
                        ?>
                    </section>

                    <? // Events ?>
                    <section class="mt-3">
                        <?
                            // events_pagination();
                        ?>

                        <ul class="cah-events">
                            <?
                                // First parameter = however many months you want to show.
                                // e.g. 0 or 1, shows 1 month, 2 = 2 months, etc.
                                print_handler($num_months_to_show);
                            ?>
                        </ul>

                        <?
                            show_more_events_handler();
                        ?>
                    </section>
                </div>
            </div>
            <?
            break;
        default:
            ?>
            <div class="row">
                <? // Filters ?>
                <section class="col-sm-3 my-3">
                    <?
                        filter_handler($format)
                    ?>
                </section>

                <? // Events ?>
                <section class="col-sm-9 mt-0">
                    <?
                        events_pagination();
                    ?>

                    <ul class="cah-events">
                        <?
                            print_handler($num_events_to_show);
                        ?>
                    </ul>

                    <?
                        show_more_events_handler();
                    ?>
                </section>
            </div>
            <?

    }
    
}

?>