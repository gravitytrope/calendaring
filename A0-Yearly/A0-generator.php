<?php
date_default_timezone_set('Atlantic/Reykjavik');

$year = 2019;

// Switch these to what ever language you want
function translate_month_name($en_month){
	// If you want custom months comment the next line and replace the strings below
	//return $en_month;

	switch($en_month){
		case 'MAY':
			return 'MAÍ';
			break;
		case 'JUN':
			return 'JÚN';
			break;
		case 'JUL':
			return 'JÚL';
			break;
		case 'AUG':
			return 'ÁGÚ';
			break;
		case 'OCT':
			return 'OKT';
			break;
		case 'NOV':
			return 'NÓV';
			break;
		case 'DEC':
			return 'DES';
			break;
		default:
			return $en_month;
			break;
	}
}

echo '<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">';

echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="841mm" height="1189mm">';

// Print out the A0 sheet
echo '<rect x="0" y="0" width="841mm" height="1189mm" style="fill:rgb(255,255,255);stroke-width:.5;stroke:rgb(0,0,0)"/>'."\n";

// Get the first week of the year
$firstDay = strtotime($year.'-01-01');
$dayOfWeek = date('w',$firstDay);

$easter = easter_date($year);

// Holiday list (Add movable holidays)
// Translate and add as needed
$holidays = array('05-01'=>'Verkalýðsdagurinn',//'May Day',
				  '01-01'=>'Nýársdagur',//'New Year\'s Day',
				  '06-17'=>'Þjóðhátíðardagurinn', //'National Day',
				  '10-24'=>'Kvennafrídagurinn',//'Women\'s Day Off',
				  '12-24'=>'Aðfangadagur',//'Christmas Eve',
				  '12-25'=>'Jóladagur',//'Christmas Day',
				  '12-26'=>'Annar í jólum', //'Day After Christmas',
				  '12-31'=>'Gamlársdagur', // 'New Year\'s Eve',
				  date('m-d',$easter)=>'Páskadagur',//'Easter',
				  date('m-d',strtotime('+1 days',$easter))=>'Annar í páskum',//'Easter Monday',
				  date('m-d',strtotime('-2 days',$easter))=>'Föstudagurinn langi',//'Good Friday',
				  date('m-d',strtotime('-3 days',$easter))=>'Skírdagur',//'Holy Thursday',
				  date('m-d',strtotime('+39 days',$easter))=>'Uppstigningardagur',//'Ascension',
				  date('m-d',strtotime('+49 days',$easter))=>'Hvítasunnudagur',//'Whit Sunday',
				  date('m-d',strtotime('+50 days',$easter))=>'Annar í hvítasunnu',//'Whit Monday',
				  date('m-d',strtotime('First Sunday June '.$year))=>'Sjomannadagurinn',
				  date('m-d',strtotime('First Monday August '.$year))=>'Verslunamanna',
				  // First Day of Summer (Added in Old Icelandic List)

				  // Birthdays!
				  // MM-DD => <Display String>
				  //'05-29'=>'• Brian',
					);

// Old Icelandic Months (right aligned) (Get these dynamically)
// Inefficient, but works
$oi_months = array();

include('../old_icelandic_calendar.php');
$oi_date = $firstDay;
for($i=0;$i<365;$i++){
	//echo $i;
	$OIDate = new OldIcelandicDate(date('Y',$oi_date),date('n',$oi_date),date('j',$oi_date));
	if ((int)($OIDate->day) == 1){
		$oi_months[date('m-d',$oi_date)] = $OIDate->month_name;
		if ($OIDate->month == 6){
			$holidays[date('m-d',$oi_date)] = "Sumardagurinn fyrsti";
		}
		if ($OIDate->month == 0){
			$holidays[date('m-d',$oi_date)] = "Kjötsúpudagurinn";
		}
	}

	$oi_date = strtotime('+1 day',$oi_date);
	
}


$offset = 0;
// Week 1
if ($dayOfWeek == 1 || $dayOfWeek == 2 || $dayOfWeek == 3 || $dayOfWeek == 4){
	$firstDay = strtotime('-'.($dayOfWeek-1).' days',$firstDay);
} else if ($dayOfWeek == 5 || $dayOfWeek == 6) {
	// Week 52/53
	// 0, 5, 6
	$firstDay = strtotime('+'.(8-$dayOfWeek).' days',$firstDay);
} else {
	$firstDay = strtotime('+1 days',$firstDay);
}

$currentDay = $firstDay;

// 13 weeks in 4 columns
for($i=0;$i<4;$i++){
	for($j=0;$j<92;$j++){
		$thickness = 1;
		$dayString = date('j',$currentDay);

		// skip the final end cap line.
		if ($j%92 != 91){
			if (date('j',$currentDay)==1){
				$thickness = 4;
				$dayString = translate_month_name(strtoupper(date('M',$currentDay)));
			}
		}

		// weekend grey boxes
		if ($j%7 == 5 || $j%7 == 6){
			echo '<rect x="'.(($i*207.75)+10).'mm" y="'.(($j*12.857)+10).'mm" width="197.75mm" height="12.857mm" style="fill:rgb(210,210,210);"/>'."\n";
		}

		// Horizontal line
		echo '<line x1="'.(($i*207.75)+10).'mm" y1="'.(($j*12.857)+10).'mm" x2="'.(($i*207.75)+207.75).'mm" y2="'.(($j*12.857)+10).'mm" style="stroke:rgb(0,0,0);stroke-width:'.$thickness.'"/>'."\n";

		// Output the day label
		if ($j%92 != 91){
			echo '<text text-anchor="start" y="'.(($j*12.857)+19).'mm" x="'.(($i*207.75)+11).'mm" font-family="\'HelveticaNeue\'" font-size="21" style="font-weight: bold;">'.$dayString.'</text>'."\n";
			
			// check for any holidays today
			if (in_array(date('m-d',$currentDay), array_keys($holidays))){
				echo '<text text-anchor="start" y="'.(($j*12.857)+19).'mm" x="'.(($i*207.75)+41).'mm" font-family="\'HelveticaNeue\'" font-size="21" fill="#585858" style="font-weight: bold;">'.$holidays[date('m-d',$currentDay)].'</text>'."\n";	
			}

			// check for any old icelandic months starting today
			if (in_array(date('m-d',$currentDay), array_keys($oi_months))){
				echo '<text text-anchor="end" y="'.(($j*12.857)+19).'mm" x="'.(($i*207.75)+206.75).'mm" font-family="\'HelveticaNeue\'" font-size="21" fill="#585858" style="font-weight: bold;">'.$oi_months[date('m-d',$currentDay)].'</text>'."\n";	
			}

			$currentDay = strtotime('+1 days',$currentDay);

		}


	}
}

echo '</svg>';

?>
