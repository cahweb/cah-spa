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
            echo "Database connection success.<br><br>";
            return $db_connection;
        }
    }
        
    $courses_data = array();
        
    $db_connection = db_connect();      
    $sql = "SELECT term, department, career, number, prefix, catalog_number, title, instruction_mode, instructor, class_start, class_end, meeting_days FROM courses";
    $result = $db_connection->query($sql);
        
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $course_data = array(
                "term" => $row['term'],
                "department" => $row['department'],
                "career" => $row['career'],
                "number" => $row['number'],
                "course" => $row['prefix'] . $row['catalog_number'],
                "title" => $row['title'],
                "mode" => $row['instruction_mode'],
                "instructor" => $row['instructor'],
                "meeting_days" => $row['meeting_days'],
                "start_time" => $row['class_start'],
                "end_time" => $row['class_end']
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

    <div class="container">
		<table id="courses" class="display" width="100%">
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
                    { data: 'number' },
                    { data: 'course' },
                    { data: 'title' },
                    { data: 'instructor' },
                    { data: 'mode' },
                    { data: 'meeting_days' },
                    { data: 'career' }
                ]
            });
        })
    </script>

    <script>
    console.log(<? print json_encode($courses_data) ?>);
    </script>

<? get_footer(); ?>
