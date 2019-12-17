<?
/*
    Upcoming Events
*/

// Shortcode used in Wordpress is the first parameter.
add_shortcode("events", "display_events");

function display_events($atts = [], $content = null) {
    // Attributes given in the shortcode call in Wordpress
    $attributes = shortcode_atts([
        "months-to-show" => 2,
        "format" => 0,
    ], $atts);

    $num_months_to_show = $attributes["months-to-show"];
    $format = $attributes["format"];

    // Flag for no events in a month.
    global $isEmpty;
    $isEmpty = FALSE;
    
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
    <?
}

?>