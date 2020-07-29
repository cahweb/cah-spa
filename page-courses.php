<? get_header(); ?>

    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.5/css/jquery.dataTables.css">
    <style>
        table.dataTable tbody th, table.dataTable tbody td {
            padding: 8px 9px;
        }

        .cursorhover:hover {
            cursor:pointer;   
        }
        a.cursorhover {
            color: #014c8c;
        }
    </style>

    <?

    if (have_posts()) {
        while ( have_posts() ) {
            the_post();
            the_content(); 
        }
    }

    ?>

    <div class="container">
		<div id="titleYear"></div>
				
		<form class="form-inline" role="form">
            <div class="form-inline form-group ml-3">
                <?
                    // $term = $term_error = $extra = "";
                    // $sql = sql_termselect();
                    // print_selectfield("changeTerm", "Change Term:&nbsp;", "", "term", "term", $term, $term_error, $extra, $sql);
                ?>
            </div>

            <div class="form-inline form-group ml-3">
                <label for="subjectSelector">Course Subject:&nbsp;</label>
                <select id="subjectSelector"><option disabled></option></select>
            </div>

            <div class="form-inline form-group ml-3">
                <label for="careerSelector">Career:&nbsp;</label>
                <select id="careerSelector"><option disabled></option></select>
            </div>
        </form>

        <div class="table-responsive">
			<table id="coursesTable" class="display responsive no-wrap" cellspacing="0" width="100%" style="font-size:0.8rem;">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Course</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Instructor</th>
                        <th>Mode</th>
                        <th>Date</th>
                        <th>Syllabus</th>
                        <th>Career</th>
                    </tr>
                </thead>

                <tfoot>
                    <tr style="vertical-align:top;">
                        <th>No.</th>
                        <th>Course</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Instructor</th>
                        <th>Mode</th>
                        <th>Date</th>
                        <th>Syllabus</th>
                        <th>Career</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/responsive/2.2.1/js/responsive.jqueryui.min.js"></script>

    <? get_footer(); ?>

    <script type="text/javascript">
    
        var id = "<?// DEPT ?>";
        var dataTemp = [];
        var subjects = [];
        var careers = [];
        var characterLimit = 100;
        var moreContent = "...<br/><a class='cursorhover'>(Expand)</a>";
        var lessContent = "<br/><a class='cursorhover'>(Collapse)</a>";
        var table = null;

        $(document).ready(function() {
            updateTable();

            $('#changeTerm').change(function(){
                updateTable();
            });

            $('#subjectSelector').change(function(){
                
                console.log("changed");  
                table.columns(1).search( $("#subjectSelector").val() ).draw();
            });

            $('#careerSelector').change(function(){
                
                console.log("changed");  
                table.columns(8).search( $("#careerSelector").val() ).draw();
            });

            $('#coursesTable tbody').on( 'click', "td:contains('Expand'), td:contains('Collapse')", function () {
                var content = table.cell( this ).data();
                if(content.length > characterLimit+moreContent.length) {
                    table.cell(this).data(table.cell(this).data().substring(0,characterLimit) + moreContent).draw(false);
                }else if($.inArray(content, dataTemp)){
                table.cell(this).data(dataTemp[content]).draw(false);
                }
            });
        });

        console.log( id );

        var updateTable = function() {
            $('#titleYear').empty();

            if ($('#changeTerm').val() == "") {
                $('#titleYear').append("<h2 style='color:#f4b350;'><?=get_semester()?></h2>");
            } else {
                $('#titleYear').append("<h2 style='color:#f4b350;'>" + $('#changeTerm').val() + "</h2>");
            }
    
            if (table != null) table.destroy();
            
            table = $('#coursesTable').DataTable({
		        "language": {
		            "emptyTable": "No courses offered"
		        },
                "responsive": true,
                "iDisplayLength": 50,
		
		        fnInitComplete: function(oSettings, json) {
                    subjects = [""];
                    careers = [""];
                    $('#subjectSelector').empty();
                    $('#careerSelector').empty();

                    var rows = $("#coursesTable").dataTable().fnGetNodes();
                    for (var i = 0; i < rows.length; i++) {
                        var content = $(rows[i]).find("td:eq(3)").html();
                        var subject = $(rows[i]).find("td:eq(1)").html();
                        var career = $(rows[i]).find("td:eq(8)").html();
                        //console.log(content);
                        //console.log(subject);
                        if (content.length > characterLimit) {
                            dataTemp[content.substring(0,characterLimit) + moreContent] = content+lessContent;
                            $("#coursesTable").dataTable().fnUpdate(content.substring(0,characterLimit) + moreContent,table.row($(rows[i])).index(),3);
                            //console.log(table.row(rows[i]));
                        }
                        if (subjects.indexOf(subject.substring(0,3)) == -1) {
                            //console.log("adding " + subject.substring(0,3));
                            subjects.push(subject.substring(0,3)); 
                        }
                        if (careers.indexOf(career.substring(0,4)) == -1) {
                            //console.log("adding " + career.substring(0,4));
                            careers.push(career.substring(0,4)); 
                        }
                    }

                    $.each(subjects, function(key, value) {  
                        //console.log("adding " + value);

                        $('#subjectSelector')
                            .append($("<option></option>")
                            .attr("value",value)
                            .text(value)); 
                    });

                    $.each(careers, function(key, value) {  
                        //console.log("adding " + value);

                    $('#careerSelector')
                        .append($("<option></option>")
                        .attr("value",value)
                        .text(value)); 
                    });
                },

                ajax: "<?= "/wp-content/themes/tbone/scripts/courses.php?term=" ?>" + $('#changeTerm').val()+"&did=" + id,

                columns: [
                    { "data": "coursenumber", "width":"2%" },
                    { "data": "catalogref", "width":"5%" },
                    { "data": "title", "width":"17%" },
                    { "data": "description", "width":"32%" },
                    { "data": "instructor", "width":"13%" },
                    { "data": "mode", "width":"5%" },
                    { "data": "dateandtime", "width":"20%" },
                    { "data": "syllabus", "width":"3%" },
                    { "data": "career", "width":"3%" }
                ],

                order: [[ 1, "asc" ]]
            });
        };
    </script>