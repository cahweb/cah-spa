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
        
    $courses_data = array();
        
    $db_connection = db_connect();      
    $sql = "SELECT courses.term, courses.career, courses.number AS course_number, CONCAT(courses.prefix, courses.catalog_number) AS course_code, courses.title, courses.instruction_mode, TIME_FORMAT(class_start, '%h:%i %p') AS course_time_start, TIME_FORMAT(class_end, '%h:%i %p') AS course_time_end, courses.meeting_days, courses.user_id, courses.department_id, CONCAT(users.fname, ' ', users.lname) AS instructor, departments.short_description AS department FROM courses INNER JOIN users ON courses.user_id = users.id INNER JOIN departments ON courses.department_id = departments.id;";
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
        
            // echo "<pre>";
            // print_r($row);
            // echo "</pre>";
        }
    } else {
        echo "NOPE";
    }
        
    $db_connection->close();
        
    // echo "<pre>";
    // print_r($courses_data);
    // echo "</pre>";

?>

<? get_header(); ?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">

<style>
    #courses > * {
        font-size: 0.86rem;
    }
</style>

    <div class="container">
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
                    </tr>
                </tfoot>
        </table>
    </div>

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('#courses').DataTable({
                data: <? print json_encode($courses_data) ?>,
                columns: [
                    { data: 'course_number' },
                    { data: 'course_code' },
                    { data: 'title' },
                    { data: 'instructor' },
                    { data: 'instruction_mode' },
                    { data: 'meeting_datetimes' },
                    { data: 'career' }
                ]
            });
        })
    </script>

<? get_footer(); ?>
