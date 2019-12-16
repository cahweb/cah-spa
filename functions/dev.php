<?

/*
    Developer Functions
    -------------------
    Used for testing and debugging. Will be removed in production.
*/

// Displays error messages and saves lives.
ini_set('display_errors', 1);
// error_reporting(E_ALL);

function spaced($string) {
    echo "<br><br>";
    echo $string;
    echo "<br><br>";
}

function spaced_array($strings) {
	if ($strings == '') {
		spaced("EMPTY ARRAY GIVEN");
	} else {
		echo "<br><br>";
		
		foreach ($strings as $string) {
			echo $string;
			echo "<br>";
		}
	
		echo "<br><br>";
	}
}

function test() {
    spaced("TEST");
}

function test_event_item() {
    $link = "https://events.ucf.edu/event/1436544/veterans-month-2019-event-civil-war-veterans-the-grand-army-of-the-republic-comes-home-exhibit/";
    $date_range = "November 7 - November 29";
    $title = "Veterans Month 2019 Event: “Civil War Veterans: The Grand Army of the Republic Comes Home\" Exhibit";
    $description = "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Laudantium, perspiciatis corrupti eveniet iure dolorem fugit culpa voluptatem, necessitatibus libero at iste perferendis illum totam quisquam adipisci mollitia, sint harum autem?";

    event_item_template($link, $date_range, $title, $description);
}

// Old function.
function display_events_index1($atts = [], $content = null, $tag = '') {
	$atts = array_change_key_case((array)$atts, CASE_LOWER);
	$events_atts = shortcode_atts([
		'path' => 'http://events.ucf.edu/calendar/3611/cah-events/',
		'numposts' => '4'
	], $atts, $tag);

	$json = array();
	$j=0;
	$title="";
	$a = $b = "";

	for ($i=-1; $i<3; $i++) {
		if ($emonth==12) {
			$emonth=1;
			$eyear=date("Y")+1;
        } else {
			$emonth = date("m")+$i;
			$eyear=date("Y");
        }
		
		$json = file_get_contents($events_atts['path'].$eyear. "/" . $emonth . "/" . "feed.json");				
        $events = (array)(json_decode($json));

		if (!empty($events)) {
			foreach ($events as $ievent) {
				if ( $j && searchArray('title', $ievent->title, $dept_events, false) && !strpos($ievent->title, 'Symphonic Band Concert')) {
				    $id = searchArray('title', $ievent->title, $dept_events, false);

	                $diff = date_diff(new DateTime($dept_events[$id]['starts']),new DateTime($ievent->starts));
	                $diff1 = date_diff(new DateTime($dept_events[$id]['ends']),new DateTime($ievent->starts));
                    $x = $diff->format("%d");
                    $y = $diff1->format("%d");

					if ($x==1 || $x==0 || $y==1 || $y==0) {                          
                        $dept_events[$id]['ends'] = $ievent->ends;
					} else {
						$dept_events[$j]['title'] = $ievent->title;
						$dept_events[$j]['url'] = $ievent->url;
						$dept_events[$j]['starts'] = $ievent->starts;
						$dept_events[$j]['ends'] = $ievent->ends;
						$dept_events[$j]['description'] = $ievent->description;
                        
                        $j++;
					}
				} else {
					$dept_events[$j]['title'] = $ievent->title;
					$dept_events[$j]['url'] = $ievent->url;
					$dept_events[$j]['starts'] = $ievent->starts;
					$dept_events[$j]['ends'] = $ievent->ends;
                    $dept_events[$j]['description'] = $ievent->description;
                    
					$j++;
				}
			} //end foreach
        } //end if(!empty($events))
	}//end for

	$title = "";
	$count = 0;

	echo '<div class="cah-events row">';

	foreach ($dept_events as $ievent) {
		if ($count == $events_atts['numposts'])
			break;

		if (date("Y-m-d H:i:s",strtotime($ievent['ends'])) < date("Y-m-d H:i:s"))
			continue;

	?>

        <div class="cah-events-item col-6" onclick="location.href='<?=$ievent['url']?>'">
            <h3>
                <? date_default_timezone_set("America/Chicago"); ?>
                <? if (date("F j", strtotime($ievent['starts'])) != date("F j", strtotime($ievent['ends']))): ?>
                <?=date("F j", strtotime($ievent['starts']))?> - <?=date("F j", strtotime($ievent['ends']))?>
                <? else: ?>
                <?=date("F j", strtotime($ievent['starts']))?>
                <? endif; ?>
            </h3>
            
            <h4><?=$ievent['title']?></h4>
            
            <div class="cah-events-description">
                <? echo strip_tags(substr($ievent['description'], 0, 200) . "..."); ?>
            </div>
        </div>

	<?
		$count++;

    }//end foreach
    
	echo "</div>";
}

function print_events() {
	$path = 'http://events.ucf.edu/calendar/3611/cah-events/';
	$numposts = 4;

	$json = array();
	$j = 0;
	$title = "";
	$a = $b = "";
	$emonth = date('m');
	$eyear = date('Y');

	for ($i=-1; $i<3; $i++) {
		// if ($emonth==12) {
		// 	$emonth=1;
		// 	$eyear=date("Y")+1;
        // } else {
		// 	$emonth = date("m")+$i;
		// 	$eyear=date("Y");
        // }
		
		$json = file_get_contents($path . $eyear. "/" . $emonth . "/" . "feed.json");				
        $events = (array)(json_decode($json));

		if (!empty($events)) {
			foreach ($events as $ievent) {
				$dept_events[$j]['title'] = $ievent->title;
				$dept_events[$j]['url'] = $ievent->url;
				$dept_events[$j]['starts'] = $ievent->starts;
				$dept_events[$j]['ends'] = $ievent->ends;
                $dept_events[$j]['description'] = $ievent->description;
                
				$j++;
			} //end foreach
        } //end if(!empty($events))
	} //end for

	$title = "";
	$count = 0;

	foreach ($dept_events as $ievent) {
		if ($count == $numposts)
			break;

		if (date("Y-m-d H:i:s",strtotime($ievent['ends'])) < date("Y-m-d H:i:s"))
			continue;

		date_default_timezone_set("America/New_York");

		// if (date("F j", strtotime($ievent['starts'])) != date("F j", strtotime($ievent['ends']))) {
		// 	event_item_template($ievent['url'], date("F j", strtotime($ievent['starts'])) . " - " . date("F j", strtotime($ievent['ends'])), $ievent['title'], strip_tags(substr($ievent['description'], 0, 200) . "..."));
		// } else {
		// 	event_item_template($ievent['url'], date("F j", strtotime($ievent['starts'])), $ievent['title'], strip_tags(substr($ievent['description'], 0, 200) . "..."));
		// }
	?>

		<div class="cah-events-item col-6" onclick="location.href='<?=$ievent['url']?>'">
            <h3>
                <? date_default_timezone_set("America/Chicago"); ?>
                <? if (date("F j", strtotime($ievent['starts'])) != date("F j", strtotime($ievent['ends']))): ?>
                <?=date("F j", strtotime($ievent['starts']))?> - <?=date("F j", strtotime($ievent['ends']))?>
                <? else: ?>
                <?=date("F j", strtotime($ievent['starts']))?>
                <? endif; ?>
            </h3>
            
            <h4><?=$ievent['title']?></h4>
            
            <div class="cah-events-description">
                <? echo strip_tags(substr($ievent['description'], 0, 200) . "..."); ?>
            </div>
        </div>

	<?
		$count++;
	}
}
?>