<? 
    // Courses Page
?>

<?
    function db_connect() {
        $db_server = "net1251.net.ucf.edu";
        $db_user = 'cah';
        $db_pass = 'cahweb';
        $db_name = 'cah';
        
        $db_connection = new mysqli($db_server, $db_user, $db_pass, $db_name);
        
        if ($db_connection->connect_error) {
            die("Database connection failed: " . $db_connection->connect_error . "<br><br>");
        } else {
            // echo "Database connection success.<br><br>";
            return $db_connection;
        }
    }

    function get_course_data() {
        $courses_data = array();
            
        $db_connection = db_connect();      
        $sql = "SELECT courses.term, courses.career, courses.number AS course_number, CONCAT(courses.prefix, courses.catalog_number) AS course_code, courses.title, courses.instruction_mode, TIME_FORMAT(class_start, '%h:%i %p') AS course_time_start, TIME_FORMAT(class_end, '%h:%i %p') AS course_time_end, courses.meeting_days, courses.user_id, courses.department_id, CONCAT(users.fname, ' ', users.lname) AS instructor, departments.short_description AS department FROM courses INNER JOIN users ON courses.user_id = users.id INNER JOIN departments ON courses.department_id = departments.id WHERE departments.ou = 'SPA' AND CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(courses.term, ' ', 2), ' ', -1) AS UNSIGNED) >= YEAR(CURDATE());";
        $result = $db_connection->query($sql);
            
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $course_data = array(
                    "career" => $row['career'],
                    "course_code" => $row['course_code'],
                    "course_number" => $row['course_number'],
                    "department" => $row['department'],
                    "department_id" => $row['department_id'],
                    "instructor" => $row['instructor'],
                    "instructor_id" => $row['user_id'],
                    "instruction_mode" => $row['instruction_mode'],
                    "meeting_datetimes" => $row['meeting_days'] . " " . date_format(date_create($row['course_time_start']), "g:i A") . " - " . date_format(date_create($row['course_time_end']), "g:i A"),
                    "term" => $row['term'],
                    "title" => $row['title'],
                );
            
                array_push($courses_data, $course_data);
            }
        } else {
            echo "There is either no course data or something went wrong trying to fetch that data.";
        }
            
        $db_connection->close();

        return $courses_data;
    }

    function get_current_term() {
        $current_year = date("Y");
        $current_month = date("n");
        $current_day = date("j");

        $current_term_season = "";
        
        // Fair chance that if it's after August 14th, it's the start of Fall semester.
        if (($current_month == 7 && $current_day > 14) || (8 <= $current_month && $current_month <= 11)) {
            $current_term_season = "Fall";
        }
        // Spring semester.
        else if ((0 <= $current_month && $current_month <= 3) || ($current_month == 4 && $current_day >= 14)) {
            $current_term_season = "Spring";
        }
        // Fair chance that if it's after May 14th, it's the start of Summer semester.
        else {
            $current_term_season = "Summer";
        }

        return array("current_term_year" => $current_year, "current_term_season" => $current_term_season);
    }

    function get_unique_terms() {
        $unique_terms = array();

        $db_connection = db_connect();
        $sql = "SELECT DISTINCT courses.term FROM courses INNER JOIN users ON courses.user_id = users.id INNER JOIN departments ON courses.department_id = departments.id WHERE departments.ou = 'SPA' AND CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(courses.term, ' ', 2), ' ', -1) AS UNSIGNED) >= YEAR(CURDATE()) ORDER BY SUBSTRING_INDEX(SUBSTRING_INDEX(courses.term, ' ', 2), ' ', -1) ASC, SUBSTRING_INDEX(courses.term, ' ', 2) DESC;";
        $result = $db_connection->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($unique_terms, $row['term']);
            }
        } else {
            echo "There is either no course data or something went wrong trying to fetch that data.";
        }

        $db_connection->close();

        return $unique_terms;
    }
?>

<? get_header(); ?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">

<style>
    #courses > * {
        font-size: 0.86rem;
    }
    #current-term {
        color: #f4b350;
    }
</style>

    <div class="container mb-5">
        <h2 id="current-term"></h2>

        <div class="mb-4 row mx-0 justify-content-center">
            <div class="mr-4">
                <label for="filter-term">Term:</label>
                <select name="filter-term" id="filter-term" class="filter-select">
                    <?
                        foreach (get_unique_terms() as $term) {
                            echo '<option value="' . $term . '">' . $term . "</option>";
                        }
                    ?>
                </select> 
            </div>

            <div class="mr-4">
                <label for="filter-subject">Subject:</label>
                <select name="filter-subject" id="filter-subject" class="filter-select">
                    <option value="All">All</option>
                    <option value="Music">Music</option>
                    <option value="Theatre">Theatre</option>
                </select> 
            </div>

            <div>
                <label for="filter-career">Career:</label>
                <select name="filter-career" id="filter-career" class="filter-select">
                    <option value="All">All</option>
                    <option value="UGRD">UGRD</option>
                    <option value="GRAD">GRAD</option>
                </select> 
            </div>
        </div>

		<table id="courses" class="display" width="100%" data-page-length="50">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Course</th>
                        <th>Title</th>
                        <th>Instructor</th>
                        <th>Mode</th>
                        <th>Date</th>
                        <th>Career</th>
                        <th>Department</th>
                        <th>Term</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>

                <tfoot>
                    <tr>
                        <th>No.</th>
                        <th>Course</th>
                        <th>Title</th>
                        <th>Instructor</th>
                        <th>Mode</th>
                        <th>Date</th>
                        <th>Career</th>
                        <th>Department</th>
                        <th>Term</th>
                    </tr>
                </tfoot>
        </table>
    </div>

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
    <script>
        let currentTerm = "<?= get_current_term()['current_term_season'] . ' ' . get_current_term()['current_term_year'] ?>";
        
        let currentTermHeader = document.getElementById("current-term");
        currentTermHeader.innerHTML = currentTerm;

        let currentTermFilter = document.getElementById("filter-term");
        currentTermFilter.value = currentTerm;
        let currentSubjectFilter = document.getElementById("filter-subject");
        currentSubjectFilter.value = "All";
        let currentCareerFilter = document.getElementById("filter-career");
        currentCareerFilter.value = "All";

        let courseData = <?= json_encode(get_course_data()) ?>;

        function filterCourseData(data, term, subject, career) {
            let filteredCourseData = [];

            data.forEach(course => {
                if (course.term == term) {
                    if (subject == "All" && career == "All") {
                        filteredCourseData.push(course);
                    }

                    if (subject != "All") {
                        if (career == "All") {
                            if (course.department == subject){
                                filteredCourseData.push(course);
                            }
                        } else {
                            if (course.department == subject && course.career == career){
                                filteredCourseData.push(course);
                            }
                        }
                    }

                    if (career != "All") {
                        if (subject == "All") {
                            if (course.career == career){
                                filteredCourseData.push(course);
                            }
                        } else {
                            if (course.career == career && course.department == subject){
                                filteredCourseData.push(course);
                            }
                        }
                    }
                }
            });

            return filteredCourseData;
        }  

        function renderCourseTable(filteredCourseData) {
            $('#courses').DataTable({
                data: filteredCourseData,
                destroy: true,
                columns: [
                    { data: 'course_number' },
                    { data: 'course_code' },
                    { data: 'title' },
                    { data: 'instructor' },
                    { data: 'instruction_mode' },
                    { data: 'meeting_datetimes' },
                    { data: 'career' },
                    { data: 'department' },
                    { data: 'term' },
                ]
            });
        }

        $(document).ready(function () {
            renderCourseTable(filterCourseData(courseData, currentTermFilter.value, currentSubjectFilter.value, currentCareerFilter.value));
        });

        $('.filter-select').change(function() {
            currentTermHeader.innerHTML = currentTermFilter.value;
            renderCourseTable(filterCourseData(courseData, currentTermFilter.value, currentSubjectFilter.value, currentCareerFilter.value));
        });
    </script>

<? get_footer(); ?>
