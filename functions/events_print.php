<?
/*
    Helper functions for events.php.
    Specifically, for printing events.
*/

function print_handler($number_events_to_show) {
    spaced($_SERVER['REQUEST_URI']);
    
    // For ease of typing.
    $activeCat = $GLOBALS['activeCat'];
    spaced($activeCat);

    foreach (events_index() as $event) {
        $start = strtotime($event->starts);
        $end = strtotime($event->ends);
    
        $category = parse_event_category($event->tags);

        if ($end >= time()) {
            if ($activeCat == "All") {
                event_item_template($event->url, $start, $end, $event->title, $category, $event->description);
            } else if (strpos($activeCat, $category) !== FALSE) {
                    event_item_template($event->url, $start, $end, $event->title, $category, $event->description);
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

// Properly formats category tags for printing.
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