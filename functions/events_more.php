<?
/*
    Helper functions for events.php.
    Specifically, for the "show more events" button and function.
*/

function show_more_events_handler() {
    // For the "Show more events" button and shows message if there are no more events.
    // TODO: Does not refresh page to the spot where the button is. Scrolls all the way back up.
    if (isset($_POST['more-events'])) {
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
        show_more_button();
    }
}

// Button template.
function show_more_button() {
    // !WARNING: Since this is using POST, most browsers will prompt you about having to resend info when you're refreshing. Minor annoyance, could probably be fixed with JS in the future.
    // If GET is used, it leaves the URL looking ugly.

    ?>
                    
    <form method="post" class="row">
        <button type="submit" name="more-events" class="btn btn-primary mx-auto">Show more events</button>
    </form>
            
    <?
}

// Pagination
function events_pagination() {
    ?>
        <div class="row my-3">
            <div class="mx-auto">
                <nav aria-label="page-navigation">
                    <ul class="pagination justify-content-center">
                        <? // Previous ?>
                        <li class="page-item disabled">
                            <a href="#" class="page-link" tabindex="-1">Previous</a>
                        </li>

                        <li class="page-item active">
                            <a href="#" class="page-link" >1</a>
                        </li>
                        <li class="page-item">
                            <a href="#" class="page-link" >2</a>
                        </li>
                        <li class="page-item">
                            <a href="#" class="page-link" >3</a>
                        </li>

                        <? // Next ?>
                        <li class="page-item">
                            <a href="#" class="page-link" >Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    <?
}

?>