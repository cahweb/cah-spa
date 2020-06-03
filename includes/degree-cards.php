<?

/*
    [degree-cards]
    -------------------
    - Shortcode used to display degree program cards on SPA.
    - Optional parameters:
        - dept: music, theatre
		- level: undergrad, minor, grad, cert
		- desc_limit: Some integer. Sets the upper chararcter limit for the program's description.
*/

add_shortcode('degree-cards', 'degree_cards_handler');

function degree_cards_handler($atts = []) {
	$attributes = shortcode_atts([
        'dept' => '',
		'level' => '',
		'desc-limit' => 250,
    ], $atts);
	
    $degree_dept = strtolower($atts['dept']);
	$degree_level = strtolower($atts['level']);

	if (!empty($degree_level)) {
		switch ($degree_level) {
			case "undergraduate":
				$degree_level = "undergrad";
				break;
			case "graduate":
				$degree_level = "grad";
				break;
			case "certificate":
				$degree_level = "cert";
				break;
		}
	}
	
	if (empty($atts['desc-limit'])) {
		$desc_limit = 250;
	} else {
		$desc_limit = $atts['desc-limit'];
	}

	ob_start();

	?>

	<div class="row mx-0">
		<? foreach (get_specific_degree_programs($degree_dept, $degree_level) as $program): ?>
		<div class="card col-lg-3 mb-4 px-0 card-hover" style="min-width: 31%; margin-right: 1rem">
			<a href="<?= $program['post_link'] ?>" style="color: inherit; text-decoration: inherit;">
				<img src="<?= $program['featured_image'] ?>" alt="Image for <?= $program['post_title'] ?>" class="card-img-top custom-card-img" height="150">

				<? if ($program['program_category'] === 'music'): ?>
				<div class="" style="height: 0.6rem; background-color: #33a6ff"></div>
				<? elseif ($program['program_category'] === 'theatre'): ?>
				<div class="" style="height: 0.6rem; background-color: #dc0f5e"></div>
				<? endif; ?>

				<div class="card-body p-3">
					<h1 class="card-title mb-3 h4 text-uppercase font-condensed"><?= $program['post_title'] ?></h1>
					<h2 class="card-subtitle mb-3 h6 font-weight-normal font-italic text-muted text-transform-none"><?= $program['subtitle'] ?></h2>

					<p class="card-text mb-3" style="font-size: 0.9rem"><?= shorten_desc($program['description'], $desc_limit) ?></p>
				</div>
			</a>
		</div>
		<? endforeach; ?>
	</div>

	<?

	return ob_get_clean();
}

function index_degree_programs() {
	$posts = get_posts(array('post_type' => 'program', 'posts_per_page' => -1, 'orderby' => 'name', 'order' => 'ASC'));
	$indexed_degree_programs = array();

	foreach ($posts as $post) {
		$each_degree_program = array();

		foreach ($post as $key => $value) {
			if ($key === "ID" || $key === "post_title" || $key === "post_type" || $key === "post_name" || $key === "post_status") {
				$each_degree_program[$key] = $value;
			}
		}
		
		// Get the link to the page for the degree program.
		$each_degree_program['post_link'] = get_the_permalink($each_degree_program['ID']);

		// Get Featured Image for the degree rogram.
		if (has_post_thumbnail($each_degree_program['ID'])) {
			$each_degree_program['featured_image'] = get_the_post_thumbnail_url($each_degree_program['ID']);
		}
		
		// Get degree_program attributes specific to the post page.
		$degree_program_atts = get_post_custom($each_degree_program['ID']);
	
		foreach ($degree_program_atts as $key => $value) {
			if (!empty($value[0])) {
				if ($key === "subtitle" || $key === "description" || $key === "level" || $key === "program_category") {
					$each_degree_program[$key] = $value[0];
				}
			}
		}

		array_push($indexed_degree_programs, $each_degree_program);
	}


	return $indexed_degree_programs;
}

function get_specific_degree_programs($degree_dept, $degree_level) {
	$filteredArr = array();
	$ogArr = index_degree_programs();

	foreach($ogArr as $program) {
		if ($program['program_category'] === $degree_dept && $program['level'] === $degree_level) {
			array_push($filteredArr, $program);
		} elseif ($program['program_category'] === $degree_dept && empty($program['level'])) {
			array_push($filteredArr, $program);
		} elseif (empty($program['program_category']) && $program['level'] === $degree_level) {
			array_push($filteredArr, $program);
		}
	}
	
	// Sorts it alphabetically by title.
	usort($filteredArr, function ($elem1, $elem2) {
		return strcmp($elem1['post_title'], $elem2['post_title']);
	});
	   
	if (empty($degree_level)) {
		// Sorts it by level.
		usort($filteredArr, function ($elem1, $elem2) {
			return strcmp($elem1['level'], $elem2['level']);
		});
	}

	return $filteredArr;
}

function shorten_desc($desc, $desc_limit) {
	$shorten_desc = substr($desc, 0, $desc_limit);
	$shorten_desc = substr($desc, 0, strrpos($shorten_desc," "));

	$newStrLen = strlen($shorten_desc);
	$punctuation = array('.', '?', '!');

	if ($newStrLen > 0 && $newStrLen < $desc_limit) {
		if (!in_array($shorten_desc[$newStrLen - 1], $punctuation)) {
			return $shorten_desc . "&nbsp;.&nbsp;.&nbsp;.";
		}

		return $shorten_desc;
	}
}

?>