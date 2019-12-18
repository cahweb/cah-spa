<?

/*
    Helper functions for events.php.
    Specifically, for filtering categories for events.
*/

// Determines how to show which events in a category selected above.
// Also responsible for the janky "active" css class for the filters.
global $isActive;
$isActive = array('', '', '', '', '');

global $activeCat;

function filter_handler($format) {
    // Primes global variables.
    parse_categories();

    /*
        Format is given by the Wordpress shortcode attribute "format".

        0 - (Default) Item list side bar
        1 - Drop down menu
    */

    switch ($format) {
        case 1:
            form_format_dropdown();
            break;
        default:
            form_format_list();
            break;
    }
}

// Default left-aligned list filter.
function form_format_list() {
    ?>
        <form method="get" class="cah-event-filter-button list-group list-group-horizontal-sm">
            <a href="<?= the_permalink(); ?>" class="list-group-item list-group-item-action <?= $GLOBALS['isActive'][0] ?>">All</a>

            <input type="submit" name="sort" value="Gallery" class="cah-event-filter-button list-group-item list-group-item-action <?= $GLOBALS['isActive'][1] ?>">
            <input type="submit" name="sort" value="Music" class="cah-event-filter-button list-group-item list-group-item-action <?= $GLOBALS['isActive'][2] ?>">
            <input type="submit" name="sort" value="SVAD" class="cah-event-filter-button list-group-item list-group-item-action <?= $GLOBALS['isActive'][3] ?>">
            <input type="submit" name="sort" value="Theatre" class="cah-event-filter-button list-group-item list-group-item-action <?= $GLOBALS['isActive'][4] ?>">
        </form>
    <?
}

// Dropdown filter.
function form_format_dropdown() {
    ?>
        <form method="get" class="dropdown">
            <a class="btn btn-primary dropdown-toggle w-100" href="https://example.com" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $GLOBALS['activeCat'] ?>
            </a>

            <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuLink">
                <a href="<?= the_permalink(); ?>" class="dropdown-item <?= $GLOBALS['isActive'][0] ?>">All</a>

                <input type="submit" name="sort" value="Gallery" class="dropdown-item <?= $GLOBALS['isActive'][1] ?>">
                <input type="submit" name="sort" value="Music" class="dropdown-item <?= $GLOBALS['isActive'][2] ?>">
                <input type="submit" name="sort" value="SVAD" class="dropdown-item <?= $GLOBALS['isActive'][3] ?>">
                <input type="submit" name="sort" value="Theatre" class="dropdown-item <?= $GLOBALS['isActive'][4] ?>">
            </div>
        </form>
    <?
}

// Determines which events to show and their path.
function parse_categories() {
    if (isset($_GET['sort'])) {
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
        $GLOBALS['isActive'][0] = "active";
        $GLOBALS['activeCat'] = 0;
        $category = 'All';
    }

    $GLOBALS['activeCat'] = $category;
}

?>