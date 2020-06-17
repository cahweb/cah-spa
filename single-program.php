<? /*

    Generates Degree Program page views.
    Input options are handled by the Common - Degree Programs CPT plugin.
        Located at: //net.ucf.edu/cah/NET1252_WEBSITES/wordpress/wp-content/plugins/common-degree-programs

*/ ?>

<? get_header(); ?>

<?

global $post;

$category_array = array();
$categories = get_the_category($post->ID);
foreach ($categories as $cd) {
    $category_array[]=$cd->cat_name;
}

$custom = get_post_custom($post->ID);

$subtitle = $custom["subtitle"][0];
$catalog = $custom["catalog"][0];
$flyer = $custom["flyer"][0];
$flyername = $custom["flyername"][0];
$program_category = $custom['program_category'][0];
$level = $custom["level"][0];

$description = $custom["description"][0];
$requirements = $custom["requirements"][0];

$name = $custom["name"][0];
$email = $custom["email"][0];
$phone = $custom["phone"][0];
$location = $custom['location'][0];
$contactfaculty = $custom['contactfaculty'][0];
$contact_info = array("name" => $name, "email" => $email, "phone" => $phone, "location" => $location);

if (!empty($custom['test'][0])) {
    $test = $custom['test'][0];
} else {
    $test = "";
}

$title = get_the_title($post->ID);

$dept = DEPT;

if (stripos($title, 'theatre') !== false) {
    $dept = 20;
} else if (stripos($title, 'music') !== false) {
    $dept = 13;
}

?>

<script src="<?php echo get_stylesheet_uri(); ?>/../library/js/jqueryUI/jquery-ui.min.js"></script>
<link href="<?php echo get_stylesheet_uri(); ?>/../library/js/jqueryUI/jquery-ui.min.css" rel="stylesheet">

<div class="container pt-4 pb-4">
    <div class="row" id="degree-div">
        <div class="col-md-8">
            <h2>Overview</h2>
            <div>
                <?php echo apply_filters("the_content", $description); ?>
            </div>
            <? if (!empty($requirements)): ?>
                <h2>Requirements</h2>
                <div>
                    <?php echo apply_filters("the_content", $requirements); ?>
                </div>
            <? endif; ?>
        </div>

        <div class="col-md-4 separated-col">

            <? if (!empty($contactfaculty)): ?>
                <h3 class="text-primary">Contact</h3>
                <?
                $sql = sql_showstaff( $dept, 0, $contactfaculty, 0, 1);
                //echo $sql;
                $result = mysqli_query(get_dbconnection(), $sql);
                check_result($result, $sql);
                $row = mysqli_fetch_assoc($result);
                mysqli_free_result($result);

                if (!empty($row['room_id'])) {
                    $sql = "select room_number, buildings.short_description,building_number from rooms left join buildings on (building_id=buildings.id) where rooms.id=" . $row['room_id'];
                    $result = mysqli_query(get_dbconnection(), $sql);
                    check_result($result, $sql);
                    $row2 = mysqli_fetch_assoc($result);
                    $outLocationHTML = "<br>Campus Location: ";
                    if (!empty($row2['building_number'])) {
                        $outLocationHTML .= "<a rel=\"external\" href=\"http://map.ucf.edu/locations/" . $row2['building_number'] . "\">";
                    }
                    $outLocationHTML .= $row2['short_description'] . $row2['room_number'];
                    if (!empty($row2['building_number'])) {
                        $outLocationHTML .= "</a>";
                    }

                } else if (!empty($row['location'])) {
                    //$outLocationHTML = "<br>Location: " . $row['location'];
					$outLocationHTML = "<br>" . $row['location'];
                }

                ?>

                <a style="color:#000; font-size:1.2em"
                   href="/faculty-staff/?id=<?= $contactfaculty; ?>"><strong><?= $row['fullname']; ?></strong></a>

                <?= $outLocationHTML; ?>
                <? if (!empty($row['email'])) echo "<br><a href='mailto:{$row['email']}'>" . $row['email'] . "</a>"; ?>
                <? if (!empty($row['phone'])) echo "<br>Phone: " . $row['phone'] . "</a>"; ?>
                <br><br>
                <!--           <p class="pt-2"><strong>Department Contact</strong><br>-->
                <!--           <a href="mailto:flfacult@ucf.edu">flfacult@ucf.edu</a></p>-->
            <? endif; ?>       
            
            <? if ($dept == 5): ?>
                <? if (empty($contactfaculty)): ?>
                 <h3 class="text-primary">Contact</h3>
                <? endif; ?>
                
                <a href="mailto:english@ucf.edu">english@ucf.edu</a>
                <br>407-823-5596
                <br>TCH 250A
            <? endif; ?>

            <? if ($dept == 22 && !in_array('Graduate',$category_array)): ?>
                <? if (empty($contactfaculty)): ?>
                 <h3 class="text-primary">Contact</h3>
                <? endif; ?>
                
                <strong style="color:#000; font-size:1.2em">SVAD Advising</strong>
                <br><a href="mailto:svadadvising@ucf.edu">svadadvising@ucf.edu</a>
                <br>407-823-1355
                <br>NSC 121
            <? endif; ?>

            <? if (!empty($catalog)): ?>
                <div class="text-center mt-3">
                    <a href="<?= $catalog; ?>" class="btn btn-primary btn-sm" target="_blank">UCF Catalog</a>
                </div>
            <? endif; ?>

            <? if (!empty($test)): ?>
                <div>
                    <h1><?= $test ?></h1>
                    <p>Testing</p>
                </div>
            <? endif; ?>

            <? if (!empty($flyer)): ?>
                <br><span style="display:block;"><a href="<?= $flyer; ?>" class="btn btn-primary btn-sm mt-3" target="_blank">
                <? echo empty($flyername)?  "Program Attachment": $flyername; ?></a></span>
            <? endif; ?>

            <?
                // Contact Information
                $i = 0;
                foreach ($contact_info as $key => $value) {
                    if (!empty($value)) {
                        if ($i === 0) {
                            echo '<h3 class="text-primary mt-4">Contact</h3>';
                        }
                        
                        echo tsh($key, $value) . "<br>";
                    }

                    $i++;
                }
            ?>

        </div>
    </div>
</div>

<?
    $post_id = get_the_ID();
    $post_atts = get_post_custom($post_id);

    echo '<div class="container">';

        dev_cont(array(
            tsh("Post ID", $post_id),
            tsh("Post Link", get_the_permalink($post_id)),
            tsh("Excerpt", get_the_excerpt($post_id)),
        ));

        // echo '<pre>';
        // print_r($post_atts);
        // echo '</pre>';

        // foreach ($post_atts as $key => $value) {
        //     if (!empty($value[0])) {
        //         echo tsh($key, $value[0]) . "<br>";
        //     }

        // }

        $sidebar_sections = maybe_unserialize(get_post_meta($post_id, 'sidebar-sections', true));
    
        foreach ($sidebar_sections as $sidebar_section) {
            foreach ($sidebar_section as $key => $value) {
                if (!empty($value)) {
                    if (is_array($value)) {
                        echo "<strong>" . $key . ":</strong><br>";
                        echo "<ul>";
                        foreach ($value as $key2 => $value2) {
                            echo "<li>" . tsh($key2, $value2) . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo tsh($key, $value) . "<br>";
                    }
                } else {
                    echo tsh($key, "EMPTY") . "<br>";
                }
            }
        }

        // echo '<pre>';
        // print_r($sidebar_sections);
        // echo '</pre>';

    echo '</div>';

?>

<?php get_footer(); ?>

