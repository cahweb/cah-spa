<?

/*
    Upcoming Events
*/

// Shortcode used in Wordpress is the first parameter.
add_shortcode("events", "display_events");

function display_events() {
    // test_str('');
    ?>

    <div class="row">
        <? // Filters ?>
        <section class="col-sm-3 my-3">
            <div class="list-group list-group-horizontal-sm">
                <a href="<?= the_permalink(); ?>" class="list-group-item list-group-item-action active">All</a>
                <a href="https://events.ucf.edu/tag/479904986/art-gallery/" class="list-group-item list-group-item-action">Gallery</a>
                <a href="https://events.ucf.edu/tag/41613216/music/" class="list-group-item list-group-item-action">Music</a>
                <a href="https://events.ucf.edu/tag/20035441/school-of-visual-arts-design/" class="list-group-item list-group-item-action">SVAD</a>
                <a href="https://events.ucf.edu/" class="list-group-item list-group-item-action">Theatre</a>
            </div>
        </section>

        <? // Events ?>
        <section class="col-sm-9 mt-0">
            <ul class="cah-events">
                <? parse_print_month_event_items(); ?>
                <? // print_events(); ?>

                <? // test_event_item(); ?>
                <? // test_event_item(); ?>
            </ul>
        </section>
    </div>
    <?
}

function event_item_template($link, $date_range, $title, $category, $description) {
    ?>
        <a class="cah-event-item" href=<?= $link ?>>
            <li>
                <h1 name="date-range"><?= $date_range ?></h1>
        
                <h2 name="title"><?= $title ?></h2>

                <h3 name="category"><?= $category ?></h3>
        
                <p name="description"><?= strip_tags(substr($description, 0, 300) . " . . . ") ?></p>
            </li>
        </a>

    <?
}

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

function parse_print_month_event_items() {
    // EST
    date_default_timezone_set("America/New_York");

    $path = "http://events.ucf.edu/calendar/3611/cah-events/";
    $current_year = date('Y');
    $current_month = date('m');

    // TODO: Write a function to get the current date and parse it into the JSON below.

    // Currently only displays 1 month of events.
    $events_json_contents = json_decode(file_get_contents($path . $current_year . "/" . $current_month . "/" . "feed.json"));

    $i = 0;

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
            
            $start = strtotime($event->starts);
            $end = strtotime($event->ends);

            $category = parse_event_category($event->tags);
            
            if ($end >= time()) {
                event_item_template($event->url, date("F j, Y", $start), $event->title, $category, $event->description);
            }

            $i++;
        }
    } else {
        // TODO: Add message saying that there's no upcoming events in the selected date range.
        spaced("EMPTY");
    }
}
?>