<?
/*
    Helper functions for events.php.
    Specifically, for printing events.
*/

// Sets timezone to EST.
date_default_timezone_set("America/New_York");

// If multiple months are wanted to be displayed by default.
function events_handler($num_months_to_show) {
    $current_year = date('Y');
    $current_month = date('m');

    // Determines which category to show.
    $filter = parse_categories();

    if ($num_months_to_show == 0 || $num_months_to_show == 1) {
        // print_month_events($current_year, $current_month, $filter);
        test_str('1');
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

            print_month_events($current_year, $current_month, $filter);
            
            $num_months_to_show--;
            $i++;
        }
    }
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
        return "$categories[1]";
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
function print_month_events($year, $month, $filter) {
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

            $category = parse_event_category($event->tags);

            if ($end >= time()) {
                event_item_template($event->url, $start, $end, $event->title, $category, $event->description);
            }
        }
    } else {
        $GLOBALS['isEmpty'] = TRUE;
        spaced("EMPTY");
    }
}

?>