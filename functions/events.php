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

    // Flag for events.
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
                    events_handler($num_months_to_show);
                ?>
            </ul>

            <?
                show_more_events_handler();
            ?>
        </section>
    </div>
    <?
}

function show_more_events_handler() {
    // For the "Show more events" button and shows message if there are no more events.
    // TODO: Does not refresh page to the spot where the button is. Scrolls all the way back up.
    if (array_key_exists('more-events', $_POST)) {
        spaced("IT WORKED!");

        if ($GLOBALS['isEmpty']) {
            ?>
                <div class="row">
                    <p class="mx-auto text-muted"><em>There are currently no more upcoming events to show.</em></p>
                            </div>
            <?
        } else {
            // TODO: More events button
        }
    } else {
        // TODO: Since this is using POST, most browsers will prompt you about having to resend info when you're refreshing. Minor annoyance, could probably be fixed with JS in the future.
        ?>
                    
            <form method="post" class="row">
                <!-- <input type="submit" name="more_events" value="Show next month's events" class="btn btn-primary mx-auto"> -->
                <button type="submit" name="more-events" class="btn btn-primary mx-auto">Show more events</button>
            </form>
                    
        <?
    }
}
?>