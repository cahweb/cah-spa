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
                <a href=https://events.ucf.edu/tag/479904986/art-gallery/" class="list-group-item list-group-item-action">Gallery</a>
                <a href="https://events.ucf.edu/tag/20035441/school-of-visual-arts-design/" class="list-group-item list-group-item-action">SVAD</a>
                <a href="https://events.ucf.edu/tag/41613216/music/" class="list-group-item list-group-item-action">Music</a>
                <a href="https://events.ucf.edu/" class="list-group-item list-group-item-action">Theatre</a>
            </div>
        </section>

        <? // Events ?>
        <section class="col-sm-9 mt-0">
            <ul class="cah-events">


                <?= test_event_item(); ?>
                <?= test_event_item(); ?>
            </ul>
        </section>
    </div>

    <?
}

function event_item_template($link, $date_range, $title, $description) {
    ?>
        <a class="cah-event-item" href=<?= $link ?>>
            <li>
                <h1 name="date-range"><?= $date_range ?></h1>
        
                <h2 name="title"><?= $title ?></h2>
        
                <p name="description"><?= $description ?></p>
            </li>
        </a>

    <?
}

function print_event_items() {
    $path = "http://events.ucf.edu/calendar/3611/cah-events/";

    // TODO: Write a function to get the current date and parse it into the JSON below.
    $event_json_contents = json_decode(array(file_get_contents($events_atts['path'] . $eyear . "/" . $emonth . "/" . "feed.json")));
}
?>