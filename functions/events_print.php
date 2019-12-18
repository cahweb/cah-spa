<?
/*
    Helper functions for events.php.
    Specifically, for printing events.
*/

// Sets timezone to EST.
date_default_timezone_set("America/New_York");


function print_handler1($num_events_to_show) {
    $current_year = date_create('Y');
    $current_month = date_create('m');
    
    // Determines which category to show.
    $filter = parse_categories();
    
    $i = 0;
    $num_months_to_show = total_number_of_Months();
    
    $path = "https://events.ucf.edu/calendar/4310/arts-at-ucf/";
    
    // Initializes the conditional below. It's repeated again to output the correct path.
    $events_json_contents = json_decode(file_get_contents($path . date_format($current_year, 'Y') . "/" . date_format($current_month, 'n') . "/" . "feed.json"));
    
    if (!empty($events_json_contents)) {
        while ($num_months_to_show > 0) {
            // Loop around to next year if the current month is December and the loop as already gone through once.
            if ($i > 0) {
                if (date_format($current_month, 'n') == 12) {
                    $current_year->modify("+1 year");
                }
                
                $current_month->modify("+1 month");
            }

            // Not DRY I know.
            $events_json_contents = json_decode(file_get_contents($path . date_format($current_year, 'Y') . "/" . date_format($current_month, 'n') . "/" . "feed.json"));

            // test();
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
                        $num_months_to_show++;
                        ?>
                            <!-- <div class="row my-5">
                                <p class="mx-auto text-muted"><em>There are currently no more active or upcoming events to listed for <span class="text-secondary"><?= date_format($current_month, 'F') . " " . date_format($current_year, 'Y') ?></span>.</em></p>
                            </div> -->
                        <?
                    
                        break;
                    }
                } else {
                    $num_months_to_show++;
                    ?>
                        <!-- <div class="row my-5">
                            <p class="mx-auto text-muted"><em>There are currently no more active or upcoming events to listed for <span class="text-secondary"><?= date_format($current_month, 'F') . " " . date_format($current_year, 'Y') ?></span>.</em></p>
                        </div> -->
                    <?
                    
                    break;
                }
            }

            $num_months_to_show--;
            $i++;
        }
    } else {
        ?>

            <div class="row my-5">
                <p class="mx-auto text-muted"><em>There are currently no more active or upcoming events to listed.</em></p>
            </div>

        <?
    }
}

function print_handler($num_events_to_show) {
    spaced("Number of events to show: " . $num_events_to_show);

    spaced("Number of events in total: " . count(events_index()));
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

// Function to index all events into an array for pagnination. This indexing function can possibly be merged with total_number_of_months();
function events_index() {
    $events = array();

    $current_year = date_create('Y');
    $current_month = date_create('m');

    // Tracks if this is the initial loop where date looping would not apply.
    $i = 0;

    $path = "https://events.ucf.edu/calendar/4310/arts-at-ucf/";
    
    // Initializes the conditional below. It's repeated again to output the correct path.
    $events_json_contents = json_decode(file_get_contents($path . date_format($current_year, 'Y') . "/" . date_format($current_month, 'n') . "/" . "feed.json"));

    while (!empty($events_json_contents)) {
        // Loop around to next year if the current month is December and the loop as already gone through once.
        if ($i > 0) {
            if (date_format($current_month, 'n') == 12) {
                $current_year->modify("+1 year");
            }
            
            $current_month->modify("+1 month");
        }

        // Not DRY, I know.
        $events_json_contents = json_decode(file_get_contents($path . date_format($current_year, 'Y') . "/" . date_format($current_month, 'n') . "/" . "feed.json"));

        foreach ($events_json_contents as $event) {
            // The date/time when each event ends.
            $end = strtotime($event->ends);
            
            // Ensures that the events are active or upcoming:
            if ($end >= time()) {
                // Pushes each event into an array.
                array_push($events, $event);
            }
        }

        $i++;
    }

    return $events;
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