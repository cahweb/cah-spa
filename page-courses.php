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
        $sql = "SELECT SUBSTRING_INDEX(courses.term, ' ', 1) AS term_season, SUBSTRING_INDEX(SUBSTRING_INDEX(courses.term, ' ', 2), ' ', -1) AS term_year, courses.career, courses.number AS course_number, CONCAT(courses.prefix, courses.catalog_number) AS course_code, courses.title, courses.instruction_mode, TIME_FORMAT(class_start, '%h:%i %p') AS course_time_start, TIME_FORMAT(class_end, '%h:%i %p') AS course_time_end, courses.meeting_days, courses.user_id, courses.department_id, CONCAT(users.fname, ' ', users.lname) AS instructor, departments.short_description AS department FROM courses INNER JOIN users ON courses.user_id = users.id INNER JOIN departments ON courses.department_id = departments.id WHERE departments.ou = 'SPA' AND CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(courses.term, ' ', 2), ' ', -1) AS UNSIGNED) >= YEAR(CURDATE());";
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
                    "term_season" => $row['term_season'],
                    "term_year" => $row['term_year'],
                    "title" => $row['title'],
                );
            
                array_push($courses_data, $course_data);
            
                // echo "<pre>";
                // print_r($row);
                // echo "</pre>";
            }
        } else {
            echo "There is either no course data or something went wrong trying to fetch that data.";
        }
            
        $db_connection->close();

        return $courses_data;
    }
        

    function get_unique_term_years() {
        $unique_years = array();

        $db_connection = db_connect();
        $sql = "SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(courses.term, ' ', 2), ' ', -1) AS term_year FROM courses INNER JOIN users ON courses.user_id = users.id INNER JOIN departments ON courses.department_id = departments.id WHERE departments.ou = 'SPA' AND CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(courses.term, ' ', 2), ' ', -1) AS UNSIGNED) >= YEAR(CURDATE());";
        $result = $db_connection->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($unique_years, $row['term_year']);
            
                // echo "<pre>";
                // print_r($row);
                // echo "</pre>";
            }
        } else {
            echo "There is either no course data or something went wrong trying to fetch that data.";
        }

        $db_connection->close();

        return $unique_years;
    }
        
    // echo "<pre>";
    // print_r(get_unique_term_years());
    // echo "</pre>";
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
        <h2 id="current-term">Test</h2>

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
                        <th>Term Season</th>
                        <th>Term Year</th>
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
                        <th>Term Season</th>
                        <th>Term Year</th>
                    </tr>
                </tfoot>
        </table>
    </div>

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
    <script>
        let termSeasons = ["Fall", "Spring", "Summer"];
        let termYears = <? print json_encode(get_unique_term_years()) ?>;
        let currentTermHeader = document.getElementById("current-term");

        let currentDate = new Date();
        let currentTermYear = termYears[0];
        let currentTermSeason;
        
        // Fair chance that if it's after August 14th, it's the start of Fall semester.
        if ((currentDate.getMonth() == 7 && currentDate.getDate() > 14) || (8 <= currentDate.getMonth() && currentDate.getMonth() <= 11)) {
            currentTermSeason = "Fall";
        }
        // Spring semester.
        else if ((0 <= currentDate.getMonth() && currentDate.getMonth() <= 3) || (currentDate.getMonth() == 4 && currentDate.getDate() >= 14)) {
            currentTermSeason = "Spring";
        }
        // Fair chance that if it's after May 14th, it's the start of Summer semester.
        else {
            currentTermSeason = "Summer";
        }

        currentTermHeader.innerHTML = currentTermSeason + " " + currentTermYear;

        $(document).ready(function () {
            $('#courses').DataTable({
                data: <? print json_encode(get_course_data()) ?>,
                columns: [
                    { data: 'course_number' },
                    { data: 'course_code' },
                    { data: 'title' },
                    { data: 'instructor' },
                    { data: 'instruction_mode' },
                    { data: 'meeting_datetimes' },
                    { data: 'career' },
                    { data: 'department' },
                    { data: 'term_season' },
                    { data: 'term_year' },
                ]
            });
        })
    </script>

<? get_footer(); ?>
