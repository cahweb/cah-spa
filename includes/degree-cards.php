<?

/*
    [degree-cards]
    -------------------
    - Shortcode used to display degree program cards on SPA.
    - Parameters:
		- dept: music, theatre
		- level: undergrad, grad
		- minor: 1, 0, ''
		- cert: 1, 0, ''
		- desc-limit: Some integer. Sets the upper chararcter limit for the program's description.
*/

add_shortcode('degree-cards', 'degree_cards_handler');

function degree_cards_handler($atts = []) {
	$attributes = shortcode_atts([
        'dept' => '',
		'level' => '',
		'minor' => '',
		'cert' => '',
		'desc-limit' => 250,
    ], $atts);
	
    $degree_dept = strtolower($atts['dept']);
	$degree_level = strtolower($atts['level']);
	$degree_minor = strtolower($atts['minor']);
	$degree_cert = strtolower($atts['cert']);

	if (!empty($degree_level) || !empty($degree_cert)) {
		switch ($degree_level) {
			case "undergraduate":
				$degree_level = "undergrad";
				break;
			case "graduate":
				$degree_level = "grad";
				break;
		}
	}

	$degree_programs_arr = array();

	// Minors
	if (!empty($degree_minor) && $degree_minor == 1) {
		$degree_programs_arr = array_merge(get_specific_degree_programs($degree_dept, $degree_level, "", ""), get_specific_degree_programs($degree_dept, $degree_level, $degree_minor, ""));
	} elseif (empty($degree_programs_arr)) {
		$degree_programs_arr = get_specific_degree_programs($degree_dept, $degree_level, $degree_minor, $degree_cert);
	}

	// Certifications
	if (!empty($degree_cert) && $degree_cert == 1 && (empty($degree_minor) || $degree_minor != 1)) {
		$degree_programs_arr = array_merge(get_specific_degree_programs($degree_dept, $degree_level, "", ""), get_specific_degree_programs($degree_dept, $degree_level, "", $degree_cert));
	}
	
	// Description limit
	if (empty($atts['desc-limit'])) {
		$desc_limit = 250;
	} else {
		$desc_limit = $atts['desc-limit'];
	}

	ob_start();

	?>

	<div class="row mx-0">
		<? foreach ($degree_programs_arr as $program): ?>
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

					<p class="card-text mb-3" style="font-size: 0.9rem"><?= $program['excerpt'] ?> <?//shorten_desc($program['excerpt'], $desc_limit) ?></p>
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

		// Get Excerpt for the degree rogram.
		if (has_post_thumbnail($each_degree_program['ID'])) {
			$each_degree_program['excerpt'] = get_the_excerpt($each_degree_program['ID']);
		}
		
		// Get degree_program attributes specific to the post page.
		$degree_program_atts = get_post_custom($each_degree_program['ID']);
	
		foreach ($degree_program_atts as $key => $value) {
			if (!empty($value[0])) {
				if ($key === "subtitle" || $key === "description" || $key === "level" || $key === "program_category" || $key === "sublevel") {
					$each_degree_program[$key] = $value[0];
				}
			}
		}

		array_push($indexed_degree_programs, $each_degree_program);
	}


	return $indexed_degree_programs;
}

function get_specific_degree_programs($degree_dept, $degree_level, $degree_minor, $degree_cert) {
	$filteredArr = array();
	$ogArr = index_degree_programs();

	if (!empty($degree_minor) && $degree_minor == 1 && (empty($degree_cert) || $degree_cert != 1)) {
		foreach($ogArr as $program) {
			if ($program['program_category'] === $degree_dept && $program['level'] === $degree_level && $program['sublevel'] === "minor") {
				array_push($filteredArr, $program);
			} elseif ($program['program_category'] === $degree_dept && empty($program['level']) && $program['sublevel'] === "minor") {
				array_push($filteredArr, $program);
			} elseif (empty($program['program_category']) && $program['level'] === $degree_level && $program['sublevel'] === "minor") {
				array_push($filteredArr, $program);
			}
		}
	} elseif (!empty($degree_cert) && $degree_cert == 1 && (empty($degree_minor) || $degree_minor != 1)) {
		foreach($ogArr as $program) {
			if ($program['program_category'] === $degree_dept && $program['level'] === $degree_level && $program['sublevel'] === "cert") {
				array_push($filteredArr, $program);
			} elseif ($program['program_category'] === $degree_dept && empty($program['level']) && $program['sublevel'] === "cert") {
				array_push($filteredArr, $program);
			} elseif (empty($program['program_category']) && $program['level'] === $degree_level && $program['sublevel'] === "cert") {
				array_push($filteredArr, $program);
			}
		}
	}
	else {
		foreach($ogArr as $program) {
			if ($program['program_category'] === $degree_dept && $program['level'] === $degree_level && empty($program['sublevel'])) {
				array_push($filteredArr, $program);
			} elseif ($program['program_category'] === $degree_dept && empty($program['level']) && empty($program['sublevel'])) {
				array_push($filteredArr, $program);
			} elseif (empty($program['program_category']) && $program['level'] === $degree_level && empty($program['sublevel'])) {
				array_push($filteredArr, $program);
			}
		}
	}
	
	// Sorts it alphabetically by title.
	usort($filteredArr, function ($elem1, $elem2) {
		return strcmp($elem1['post_title'], $elem2['post_title']);
	});

	return $filteredArr;
}

function shorten_desc($desc, $desc_limit) {
	$ogStrLen = strlen($desc);
	
	if ($ogStrLen <= $desc_limit) {
		$shorten_desc = $desc;
	} else {
		$shorten_desc = substr($desc, 0, $desc_limit);
		$shorten_desc = substr($desc, 0, strrpos($shorten_desc, " "));
	}

	$newStrLen = strlen($shorten_desc);
	$punctuation = array('.', '?', '!');

	if ($newStrLen > 0 && $newStrLen < $desc_limit) {
		if (!in_array($shorten_desc[$newStrLen - 1], $punctuation)) {
			return $shorten_desc . "&nbsp;.&nbsp;.&nbsp;.";
		}

		return $shorten_desc;
	}
}

/*
    [section-cards]
    -------------------
	- Shortcode used to display section cards on SPA.
		- `section` is drawn from the page tags if it "about" and "beyond".
			- Otherwise, it can be "studio" or "ensemble".
		- Ensure that the proper category for Music and Theatre are assigned to have the right colored bar.
    - Parameters:
		- section: about, beyond, ensemble, studio
		- desc-limit: Some integer. Sets the upper chararcter limit for the program's description.
*/

add_shortcode('section-cards', 'section_cards_handler');

function section_cards_handler($atts = []) {
	$attributes = shortcode_atts([
        'section' => '',
		'desc-limit' => 250,
    ], $atts);
	
    $section = strtolower($atts['section']);
	
	// Description limit
	if (empty($atts['desc-limit'])) {
		$desc_limit = 250;
	} else {
		$desc_limit = $atts['desc-limit'];
	}

	$cards = array();
	$pages = array();
	$se_flag = false;

	if ($section === "studio") {
		$pages = get_posts(array('post_type' => 'studio', 'posts_per_page' => -1));
		$se_flag = true;
	} else if ($section === "ensemble") {
		$pages = get_posts(array('post_type' => 'ensemble', 'posts_per_page' => -1));
		$se_flag = true;
	} else {
		$pages = get_pages();
	}

	foreach ($pages as $page) {
		$push_flag = false;

		if ($page->post_status === "publish") {
			$category = "";

			if (has_tag($section, $page)) {
				if (has_category("Music", $page)) {
					$category = "music";
				} elseif (has_category("Theatre", $page)) {
					$category = "theatre";
				}

				$push_flag = true;
			} else if ($se_flag) {
				$category = ucfirst($section);

				$push_flag = true;
			}

			// echo "ID: " . $page->ID . "<br>";
			// echo "section: " . $section . "<br>";
			// echo "post_status: " . $page->post_status . "<br>";

			// echo "post_link: " . get_page_link($page) . "<br>";
			// echo "featured_image: " . get_the_post_thumbnail_url($page->ID) . "<br>";
			// echo "post_title: " . $page->post_title . "<br>";
			// echo "category: " . $category . "<br>";
			// echo "subtitle: " . get_the_subtitle($page, '', '', false) . "<br>";
			// echo "excerpt: " . $page->post_excerpt . "<br>";
	
			// echo "<br>";

			if ($push_flag) {
				array_push($cards, array(
					"post_link" => get_page_link($page),
					"featured_image" => get_the_post_thumbnail_url($page->ID),
					"post_title" => $page->post_title,
					"post_category" => $category,
					"subtitle" => get_the_subtitle($page, '', '', false),
					"excerpt" => $page->post_excerpt,
				));
			}
		}
	}
	
	// Sort cards by `post_title`.
	usort($cards, function($a, $b) {
		return $a['post_title'] <=> $b['post_title'];
	});

	return create_cards($cards);
}

function create_cards($cards) {
	$music_color = "#33a6ff";
	$theatre_color = "#dc0f5e";
	$default_color = "#ffcc00";
	
	ob_start();

	?>

	<div class="row mx-0">
		<? foreach ($cards as $card): ?>
		
		<?
			$card_color = $default_color;

			if ($card['post_category'] === 'music' || "studio" || "ensemble") {
				$card_color = $music_color;
			} elseif ($card['post_category'] === 'theatre') {
				$card_color = $theatre_color;
			}
		?>

		<div class="card col-lg-3 mb-4 px-0 card-hover" style="min-width: 31%; margin-right: 1rem">
			<a href="<?= $card['post_link'] ?>" style="color: inherit; text-decoration: inherit;">
				<? if ($card['featured_image']): ?>
				<img src="<?= $card['featured_image'] ?>" alt="Image for <?= $card['post_title'] ?>" class="card-img-top custom-card-img" height="150">
				<? else: ?>
				<div class="" style="height: 150px; background-color: <?= $card_color ?>"></div>
				<? endif; ?>

				<div class="" style="height: 0.6rem; background-color: <?= $card_color ?>"></div>

				<div class="card-body p-3">
					<h1 class="card-title mb-3 h4 text-uppercase font-condensed"><?= $card['post_title'] ?></h1>
					<h2 class="card-subtitle mb-3 h6 font-weight-normal font-italic text-muted text-transform-none"><?= $card['subtitle'] ?></h2>

					<p class="card-text mb-3" style="font-size: 0.9rem"><?= $card['excerpt'] ?> <?//shorten_desc($card['excerpt'], $desc_limit) ?></p>
				</div>
			</a>
		</div>
		<? endforeach; ?>
	</div>

	<?

	return ob_get_clean();
}

?>