<?
/*
    Helper functions for events.php.
    Specifically, for generating pagination and links.
*/

global $current_page;

// Navigation for event pages.
function events_pagination($num_events_to_show) {
    $GLOBALS['events'] = events_index();
    $GLOBALS['num_total_events'] = count($GLOBALS['events']);

    // Number of pages to generate for pagination.
    $GLOBALS['num_of_pages'] = intdiv($GLOBALS['num_total_events'], $num_events_to_show) + 1;

    // Determines and applies style based on what page is active.
    $current_page = 1;
    
    // For ease of typing.
    $activeCat = $GLOBALS['activeCat'];

    if ($activeCat !== "All") {
        test();
    }

    ?>
        <div class="row my-3">
            <div class="mx-auto">
                <nav aria-label="page-navigation">
                    <ul class="pagination justify-content-center">
                        <? // Previous ?>
                        <li class="page-item <?= $status ?>">
                            <a href="#" class="page-link" tabindex="-1">«</a>
                        </li>

                        <?
                            $i = 1;
                                
                            while ($i <= $GLOBALS['num_of_pages']) {
                                ?>
                                    <li class="page-item">
                                        <a href="<?= $i ?>" class="page-link" tabindex="-1"><?= $i ?></a>
                                    </li>
                                <?

                                $i++;
                            }
                        ?>


                            <? // Next ?>
                            <li class="page-item">
                                <a href="#" class="page-link" >»</a>
                            </li>
                        </ul>
                </nav>
            </div>
        </div>
    <?
}

?>