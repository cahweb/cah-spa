<?
/*
    Helper functions for events.php.
    Specifically, for printing events.
*/

// Sets timezone to EST.
date_default_timezone_set("America/New_York");

// If multiple months are wanted to be displayed by default.
function print_handler($num_months_to_show) {
    $current_year = date_create('Y');
    $current_month = date_create('m');

    // Determines which category to show.
    $filter = parse_categories();

    $i = 0;

    while ($num_months_to_show > 0) {
        // Loop around to next year if the current month is December and the loop as already gone through once.
        if ($i > 0) {
            if (date_format($current_month, 'n') == 12) {
                $current_year->modify("+1 year");
            }

            $current_month->modify("+1 month");
        }

        // print_month_events($current_year, $current_month, $filter);
        print_month_events($current_year, $current_month, $filter, $num_months_to_show);

        $num_months_to_show--;
        $i++;
    }
}

// Only prints 1 month's worth of events.
function print_month_events($year, $month, $filter, $num_of_months) {
    $path = "https://events.ucf.edu/calendar/4310/arts-at-ucf/";
    
    $events_json_contents = json_decode(file_get_contents($path . date_format($year, 'Y') . "/" . date_format($month, 'n') . "/" . "feed.json"));

    // Number of events listed in the JSON file.
    // spaced("# of events " . count($events_json_contents));

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
            
            /*
                TODO: Sort by days? Remove continguous duplicates?
                For now, I'm ignoring removing contiguous dupes, because sometimes they happen at different times. So, now I just display the times instead.
            */
            
            $start = strtotime($event->starts);
            $end = strtotime($event->ends);

            $category = parse_event_category($event->tags);

            if ($end >= time()) {
                if ($filter == "All") {
                    event_item_template($event->url, $start, $end, $event->title, $category, $event->description);
                } else if (strpos($filter, $category) !== FALSE) {
                        event_item_template($event->url, $start, $end, $event->title, $category, $event->description);
                } else {
                    ?>
                        <div class="row my-5">
                            <p class="mx-auto text-muted"><em>There are currently no more active or upcoming events to listed for <span class="text-secondary"><?= date_format($month, 'F') . " " . date_format($year, 'Y') ?></span>.</em></p>
                        </div>
                    <?
                
                    break;
                }
            } else {
                ?>
                    <div class="row my-5">
                        <p class="mx-auto text-muted"><em>There are currently no more active or upcoming events to listed for <span class="text-secondary"><?= date_format($month, 'F') . " " . date_format($year, 'Y') ?></span>.</em></p>
                    </div>
                <?
                
                break;
            }
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

?>