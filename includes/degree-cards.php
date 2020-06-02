<?

/*
    [degree-cards]
    -------------------
    - Shortcode used to display degree program cards on SPA.
    - Optional parameters:
        - dept: music, theatre
        - level: undergrad, minor, grad, cert
*/

add_shortcode('degree-cards', 'degree_cards_handler');

function degree_cards_handler($atts = []) {
	$attributes = shortcode_atts([
        'dept' => '',
        'level' => '',
    ], $atts);

    $degree_dept = $atts['dept'];
    $degree_level = $atts['level'];

	ob_start();

	?>

	<div id="app">
		<div class="row mx-0">
			<div v-for="degreeProgram in abcDegreePrograms" class="card col-lg-3 mb-4 px-0 card-hover" style="min-width: 31%; margin-right: 1rem">
				<a v-bind:href="degreeProgram.post_link" style="color: inherit; text-decoration: inherit;">
					<img v-bind:src="degreeProgram.featured_image" v-bind:alt="'Image for ' + degreeProgram.post_title" class="card-img-top custom-card-img " height="150">

					<div class="bg-primary" style="height: 0.6rem"></div>

					<div class="card-body p-4">
						<h1 class="card-title mb-3 h4">{{ degreeProgram.post_title }}</h1>
						<h2 class="card-subtitle mb-4 h6 font-weight-normal font-italic text-capitalize text-muted">{{ degreeProgram.subtitle }}</h2>

						<p class="card-text mb-4">{{ shortenDescription(degreeProgram.description) }}</p>
					</div>
				</a>
			</div>
		</div>
	</div>

	<?

	// // Most up-to-date developer version of Vue.js.
	// echo '<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>';

	// Production version 2.6.11.
	echo '<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>';

	?>

	<script>
		new Vue({
			el: "#app",
			data: {
				degreePrograms: <? print json_encode(index_degree_programs()) ?>,
				degreeFilters: ["Music", "Theatre"],
				degreeLevels: ["undergrad", "grad", "minor", "cert"],

				selectedDept: "<?= $degree_dept ?>",
				selectedLevel: "<?= $degree_level ?>",
			},
			computed: {
				abcDegreePrograms: function() {
					this.sortNestedArrABC(this.degreePrograms, 'post_title')

					this.sortNestedArrABC(this.degreePrograms, 'level')

					sortedArr = this.sortArrByLevel(this.degreePrograms, this.selectedLevel)

					return this.filterByDegreeProgram(sortedArr, this.selectedDept)
				}
			},
			methods: {
				filterByDegreeProgram: function(degreePrograms, filter) {
					var filteredArr = []
					
					if (filter) {
						degreePrograms.forEach(function(item) {
							if (item['program_category'] === filter.toLowerCase()) {
								filteredArr.push(item);
							}
						})
						
						return filteredArr
					} else {
						return degreePrograms
					}
				},
				getFullDegreeLevel: function(degreeLevel) {
					switch (degreeLevel) {
						case "undergrad":
							return "Undergraduate"
						case "grad":
							return "Graduate"
						case "minor":
							return "Minor"
						case "cert":
							return "Certificate"
						default:
							return ""
					}
				},
				shortenDescription: function(description) {
					if (description) {
						var str = description.replace(/(\n|<br>|<p>|<\/p>|<span>|<\/span>|<li>|<\/li>)/igm, " ").trim()
						str = str.replace(/(\s\s+)/igm, " ").trim()
						str = str.replace(/(<a.*?>|<\/a>|<strong>|<\/strong>|<ul>|<\/ul>)/igm, "").trim()
						
						var strArr = str.split(".", 2)
						var shortDesc = strArr[0].concat(".").concat(strArr[1])
						
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
				sortNestedArrABC: function(arr, criteria) {
					// Sorts the degree programs alphabetically.
					arr.sort(function(a, b) {
						var x = a[criteria]
						var y = b[criteria]

						if (x < y) {
							return -1;
						} else if (x > y) {
							return 1;
						} else {
							return 0;
						}
					})
				},
				sortArrByLevel: function(arr, filter) {
					var undergrad = []
					var minor = []
					var grad = []
					var cert = []
					
					arr.forEach(function(item) {
						switch (item['level']) {
							case "undergrad":
								undergrad.push(item);
								break;
							case "minor":
								minor.push(item);
								break;
							case "grad":
								grad.push(item);
								break;
							case "cert":
								cert.push(item);
								break;
						}
					})

					if (filter.toLowerCase()) {
						switch (filter) {
							case "undergrad":
								return undergrad
							case "minor":
								return minor
							case "grad":
								return grad
							case "cert":
								return cert
						}
					} else {
						return undergrad.concat(minor.concat(grad.concat(cert)))
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
					if ($key === "level") {
						$each_degree_program[$key] = "Test";
					}

					$each_degree_program[$key] = $value[0];
				}
			}
		}

		array_push($indexed_degree_programs, $each_degree_program);
	}


	return $indexed_degree_programs;
}

?>