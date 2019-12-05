<?

/*
    Upcoming Events
*/

// Shortcode used in Wordpress is the first parameter.
add_shortcode("events", "display_events");

function display_events() {
    // Flag for events.
    global $isEmpty;
    $isEmpty = FALSE;
    
    // Determines how to show which events in a category selected above.
    // Also responsible for the janky "active" css class for the filters.
    global $isActive;
    $isActive = array('', '', '', '', '');
    $category = parse_categories();
    
    ?>

    <div class="row">
        <? // Filters ?>
        <section class="col-sm-3 my-3">
            <form method="get" class="list-group list-group-horizontal-sm">
                <a href="<?= the_permalink(); ?>" class="list-group-item list-group-item-action <?= $GLOBALS['isActive'][0] ?>">All</a>

                <input type="submit" name="sort" value="Gallery" class="list-group-item list-group-item-action <?= $GLOBALS['isActive'][1] ?>">
                <input type="submit" name="sort" value="Music" class="list-group-item list-group-item-action <?= $GLOBALS['isActive'][2] ?>">
                <input type="submit" name="sort" value="SVAD" class="list-group-item list-group-item-action <?= $GLOBALS['isActive'][3] ?>">
                <input type="submit" name="sort" value="Theatre" class="list-group-item list-group-item-action <?= $GLOBALS['isActive'][4] ?>">
            </form>
        </section>

        <? // Events ?>
        <section class="col-sm-9 mt-0">
            <ul class="cah-events">
                <?
                    // Prints however many months you want to show.
                    // 0 or 1, shows 1 month, 2 = 2 months, etc.
                    event_months_to_show(2, $category);
                ?>
            </ul>

            <?
                show_more_events_handler();
            ?>
        </section>
    </div>
    <?
}

// Handles individual event's html. Description length is shorted to 300 characters.
function event_item_template($link, $start, $end, $title, $category, $description) {
    ?>
        <a class="cah-event-item" href=<?= $link ?>>
            <li>
                <h1 name="date-range"><?= date("F j, Y", $start) ?> <span class="mr-2">,</span> <span class=""><?= date("g a", $start) . " &ndash; " . date("g a", $end) ?></span></h1>

                <h2 name="title"><?= $title ?></h2>
        
                <h3 name="category"><?= date("g a", $start) . " &ndash; " . date("g a", $end) ?></h3>

                <h4 name="category"><?= $category ?></h3>
        
                <p name="description"><?= strip_tags(substr($description, 0, 300) . " . . . ") ?></p>
            </li>
        </a>

    <?
}

// Helper function for parse_print_month_events().
function parse_event_category($tags) {
    $categories = array("Gallery", "Music", "SVAD", "Theatre");

    if (strtolower($tags[0]) == "music") {
        return $categories[1];
    } else if (strtolower($tags[0]) == "theatre ucf") {
        return $categories[3];
    } else {
        // It'll be SVAD. Seems like "art gallery" always goes with SVAD.
        // This else statement depends on "art gallery" always being a tag with SVAD.

        // Checks for "art gallery" tag.
        $gallery = false;
        
        // If statement only needed to remove warning about providing an invalid input, since PHP wants you to check for empty arrays before looping them.
        if (!empty($tags)) {
            foreach ($tags as $tag1) {
                if (strtolower($tag1) == "art gallery") {
                    $gallery = true;
                }
            }
        }

        if ($gallery === true) {
            return $categories[0] . ", " . $categories[2];
        } else {
            return $categories[2];
        }
    }
    
}

// Only prints 1 month's worth of events.
function parse_print_month_events($year, $month, $category) {
    $path = "http://events.ucf.edu/calendar/3611/cah-events/";
    
    $events_json_contents = json_decode(file_get_contents($path . $year . "/" . $month . "/" . "feed.json"));

    if (!empty($events_json_contents)) {
        foreach ($events_json_contents as $event) {
            /*
                Relevant JSON categories from the events feed:
                ----------------------------------------------
                -> url
                -> title
                -> starts
                -> ends
                -> tags
                -> description
            */
            
            // TODO: Sort by days? Remove continguous duplicates?
            // For now, I'm ignoring removing contiguous dupes, because sometimes they happen at different times. So, now I just display the times instead.
            
            $start = strtotime($event->starts);
            $end = strtotime($event->ends);
            
            // TODO: Make else to show "Gallery, SVAD" when sorting just "Gallery".
            if ($category == '') {
                $category = parse_event_category($event->tags);
            }
            
            if ($end >= time()) {
                event_item_template($event->url, $start, $end, $event->title, $category, $event->description);
            }
        }
    } else {
        $GLOBALS['isEmpty'] = TRUE;
    }
}

// Sets timezone to EST.
date_default_timezone_set("America/New_York");

// If multiple months are wanted to be displayed by default.
function event_months_to_show($num_months_to_show, $category) {
    $current_year = date('Y');
    $current_month = date('m');

    if ($num_months_to_show == 0 || $num_months_to_show == 1) {
        parse_print_month_events($current_year, $current_month, $category);
    } else {
        $i = 0;

        while ($num_months_to_show > 0) {
            // Loop around to next year if the current month is December and the loop as already gone through once.
            if ($i > 0) {
                if ($current_month == 12) {
                    $current_year++;
                    $current_month = 1;
                } else {
                    $current_month++;
                }
            }

            parse_print_month_events($current_year, $current_month, $category);
            
            $num_months_to_show--;
            $i++;
        }
    }
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

// Determines how to show which events in a category selected above.
function parse_categories() {
    if (array_key_exists('sort', $_GET)) {
        $category = $_GET['sort'];
        
        switch ($category) {
            case "Gallery":
                $GLOBALS['isActive'][1] = "active";
                break;
            case "Music":
                $GLOBALS['isActive'][2] = "active";
                break;
            case "SVAD":
                $GLOBALS['isActive'][3] = "active";
                break;
            case "Theatre":
                $GLOBALS['isActive'][4] = "active";
                break;
        }
    } else {
        $category = '';
        $GLOBALS['isActive'][0] = "active";
    }
}
?>