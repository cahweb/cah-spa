<?

/*
    Developer Functions
    -------------------
    Used for testing and debugging. Will be removed in production.
*/

// Displays error messages and saves lives.
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

function spaced($string) {
    echo "<br><br>";
    echo $string;
    echo "<br><br>";
}

function tsh($label, $data) {
    if ($data == '' || $data == NULL) {
        return "<strong>" . $label . "</strong>: <em class='text-muted'>Data does not exist.</em>";
    } else {
        return "<strong>" . $label . "</strong>: " . $data;
    }
}

function dev_cont($strings) {
	?>
	<div class="" style="margin: 5% 0;">
        <div class="" style="   width: 90%;
                                padding: 2%;
                                margin: auto auto;
                                background-color: #f9e7c9;
                                border-color: #eddaba;
                                border-style: solid;
                                border-radius: 8px;
        ">

		<?
			if ($strings == '') {
				spaced("EMPTY ARRAY GIVEN");
			} else {
				foreach ($strings as $string) {
					echo $string . "<br>";
				}
			}
		?>

		</div>
	</div>
	<?
}

add_shortcode('dev-spa', 'dev_spa_handler');

function dev_spa_handler() {
	$posts = get_posts(array('post_type' => 'program', 'posts_per_page' => -1, 'orderby' => 'name', 'order' => 'ASC'));
	
	dev_cont(array(
		tsh("Post ID", get_the_ID()),
		tsh("Posts", count($posts)),
	));

	// echo '<pre>';

	// 	// print_r(get_categories());
	// 	// print_r(get_the_category());
	// 	// print_r(get_posts(array('post_type' => 'program')));	
		
	// echo '</pre>';

	// foreach ($posts as $post) {
	// 	echo '<pre>';
	// 	print_r($post);
	// 	echo '</pre>';
	// }

	foreach ($posts as $post) {
		foreach ($post as $key => $value) {
			if ($key === "ID" || $key === "post_title" || $key === "post_type" || $key === "post_name" || $key === "post_status") {
				echo tsh($key, $value) . "<br>";
			}
		}

		echo "<br>";
	}

	// $post_link = get_the_permalink($post_id);
	// echo tsh('Post Link', $post_link) . "<br>";
}

?>