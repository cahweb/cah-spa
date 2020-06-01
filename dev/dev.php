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
	ob_start();

	?>

	<div id="app">
		<div v-for="degreeFilter in degreeFilters">
			<h2>{{ degreeFilter }}</h2>
		
			<div class="row mx-0">
				<div v-for="degreeProgram in filteredDegreePrograms(degreePrograms, degreeFilter)" class="card col-lg-3 mb-4 px-0" style="min-width: 31%; margin-right: 1rem">
					<img v-bind:src="degreeProgram.featured_image" v-bind:alt="'Image for ' + degreeProgram.post_title" class="card-img-top">

					<div class="bg-primary" style="height: 0.6rem"></div>

					<div class="card-body p-4">
						<h4 class="card-title mb-3">{{ degreeProgram.post_title }}</h4>
						<h5 class="card-subtitle mb-4 h6 font-weight-normal font-italic text-muted">{{ degreeProgram.subtitle }}</h5>

						<p class="card-text mb-4">{{ shortenDescription(degreeProgram.description) }}</p>

						<a v-bind:href="degreeProgram.post_link" class="btn btn-primary">Learn More</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?

	if ($dev) {
		// Most up-to-date developer version of Vue.js.
		echo '<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>';
	} else {
		// Production version 2.6.11.
		echo '<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>';
	}

	?>

	<script>
		new Vue({
			el: "#app",
			data: {
				degreePrograms: <? print json_encode(index_degree_programs()) ?>,
				degreeFilters: ["Music", "Theatre"],
				degreeLevel: ["Undergraduate", "Graduate", "Minor", "Certificate"]
			},
			methods: {
				filteredDegreePrograms: function(degreePrograms, filter) {
					var filteredArr = []

					degreePrograms.forEach(function(item) {
						if (item['program_category'] === filter.toLowerCase()) {
							filteredArr.push(item);
						}
					})

					return filteredArr
				},
				shortenDescription: function(description) {
					if (description) {
						var str = description.replace(/(\n|<br>|<p>|<\/p>|<span>|<\/span>|<li>|<\/li>)/igm, " ").trim()
						str = str.replace(/(\s\s+)/igm, " ").trim()
						str = str.replace(/(<a.*?>|<\/a>|<strong>|<\/strong>|<ul>|<\/ul>)/igm, "").trim()
						
						var strArr = str.split(".", 2)
						var shortDesc = strArr[0].concat(".").concat(strArr[1])

						console.log(shortDesc);
						
						var strLen = shortDesc.length
						var preferredStrLen = 250
						
						if (strLen >= preferredStrLen) {
							return shortDesc.substr(0, preferredStrLen) + " . . ."
						} else {
							// If the last sentence does not contain a period, add one.
							if (shortDesc.substr(str.length - 1, shortDesc.length).trim() !== ".") {
								shortDesc += "."
							}
	
							return shortDesc
						}
					}
                },
			}
		})
	</script>

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

?>