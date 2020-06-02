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

?>