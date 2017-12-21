<?php
if(!isset($_POST['action'])){
	header("Location: index.html");
	}
	
echo '<div id="container" class="centered">';

$usr = $_POST['rno'];
$pswd = $_POST['pswd'];
$temp = substr($usr,0,4).'-'.substr($usr,4,2).'-'.substr($usr,6,3).'-'.substr($usr,9,3);
$usr = $temp;
require('support.php');

$indexPage = grab_page('http://www.vce.ac.in/index.aspx');
$dom = new domDocument;
@$dom->loadHTML($indexPage);
$viewState = urlencode($dom->getElementById('__VIEWSTATE')->getAttribute('value'));
$eventValidation = urlencode($dom->getElementById('__EVENTVALIDATION')->getAttribute('value'));

$details = login('http://www.vce.ac.in/index.aspx','__VIEWSTATE='.$viewState.'&__EVENTVALIDATION='.$eventValidation.'&txtLoginID='.$usr.'&txtPWD='.$pswd.'&btnLogin=Go%21');

$dom = new domDocument;

@$dom->loadHTML($details);
$dom->preserveWhiteSpace = false;
$xpath = new DOMXpath($dom);

$name = $xpath->query('//*[@id="divStudInfo"]/table/tr[3]/td[2]');
$year = $xpath->query('//*[@id="divStudInfo"]/table/tr[9]/td[2]');
$sem = $xpath->query('//*[@id="divStudInfo"]/table/tr[10]/td[2]');
$name = $name->item(0)->nodeValue;
$name = strtolower($name);
$name = ucwords($name);
echo '<p>Name: '.$name.'</p>';
echo '<br/><br/>';

$summaryTableRows = $xpath->query('//*[@id="divStudySummary"]/table/tr');
//echo 'Length: '.$summaryTableRows->length;
$requiredRow='';
for($i=2;$i<$summaryTableRows->length-1;$i++){
		//echo $summaryTableRows->item($i)->getElementsByTagName('td')->item(5)->textContent;
		if($summaryTableRows->item($i)->getElementsByTagName('td')->item(5)->textContent=='Studying'){
				$requiredRow = $summaryTableRows->item($i);
				break;
			}
}
$att = $requiredRow->getElementsByTagName('td')->item(8)->getElementsByTagName('a')->item(0)->textContent;
//echo $att;

$url_raw = $requiredRow->getElementsByTagName('td')->item(8)->getElementsByTagName('a')->item(0)->getAttribute('onclick');
$url_len = strlen($url_raw);
$url_att = substr($url_raw,7,$url_len-9);
//echo $url_att;
//echo $requiredRow->ownerDocument->saveXML($requiredRow);

$full_attDetails = grab_page('www.vce.ac.in/'.$url_att);
//echo $full_attDetails;
$dom = new domDocument;

@$dom->loadHTML($full_attDetails);
$dom->preserveWhiteSpace = false;
$xpath = new DOMXpath($dom);
$tableHeader = $xpath->query('//*[@id="divAttendance"]/table[1]/tr[3]')->item(0);
//$temp2 = $xpath->query('//*[@id="divAttendance"]/table[1]/tr[3]/th[1]');
//$tableHeader = $tableHeader->removeChild($temp2);
//echo $temp2->ownerDocument->saveXML($temp2);
//$tableHeader = $tableHeader->removeChild($tableHeader->getElementsByTagName('td')->item(1));
//$tableHeader->getElementsByTagName('th')->item(0)->removeChild;
$lastButOneRow = $xpath->query('//*[@id="divAttendance"]/table[1]/tr[last()-1]')->item(0);
//$lastButOneRow->getElementsByTagName('td')->item(1)->removeChild;
//$lastButOneRow->getElementsByTagName('td')->item(0)->removeChild;
$lastRow = $xpath->query('//*[@id="divAttendance"]/table[1]/tr[last()]')->item(0);
//$lastRow->getElementsByTagName('td')->item(1)->removeChild;
//$lastRow->getElementsByTagName('td')->item(0)->removeChild;
echo '<table id="att_summary" border="1"  width="100%">';
echo $tableHeader->ownerDocument->saveXML($tableHeader);
echo $lastButOneRow->ownerDocument->saveXML($lastButOneRow);
//echo '<br/>';
echo $lastRow->ownerDocument->saveXML($lastRow);
//echo '<br/>';
echo '</table>';
echo '<br/><br/>';
$tab_2_heading = $xpath->query('//*[@id="divAttendance"]/table[2]/tr[1]/td/b')->item(0)->textContent;
$att_table_index = 2;
if($tab_2_heading=='Extra Attendance '){
	$att_table_index++;
	//*[@id="divAttendance"]/table[2]/tbody/tr[2]
	$extra_att_list = $xpath->query('//*[@id="divAttendance"]/table[2]')->item(0);
	echo $extra_att_list->ownerDocument->saveXML($extra_att_list); 
}
echo '<br/><br/>';

$totalClasses = $xpath->query('//*[@id="divAttendance"]/table['.$att_table_index.']/tr[3]/td[4]')->item(0)->textContent;
$totalPresent = $xpath->query('//*[@id="divAttendance"]/table['.$att_table_index.']/tr[4]/td[4]')->item(0)->textContent;
$totalPresent = getCorrect($totalPresent);
function getCorrect($present){
	$len = strlen($present);
	if($len>3){
		$index = strpos($present,'=');	
		$present = substr($present,$index+1,strlen($present)-$index-1);
	}
	return $present;
}
$totalClasses = intval($totalClasses);
$totalPresent = intval($totalPresent);
$totalAbsent = $totalClasses - $totalPresent;
//echo $totalClasses;
//echo '<br/>';
//echo $totalPresent;
//echo '<br/>';
//echo $totalAbsent;
echo '<input id="total_classes" type="hidden" value="'.$totalClasses.'">';
echo '<input id="total_present" type="hidden" value="'.$totalPresent.'">';
echo '<p style="font-size:25px ">Attendance: '.$att.'%</p>';
echo '<br/>';

$noOfClassesToAttend = 0;
if($att<75){
	$noOfClassesToAttend = (3*$totalClasses-4*$totalPresent);
	echo '<p style="font-size:20px ">You have to attend '.$noOfClassesToAttend.' classes continuously to make 75%</p>';
}else{
	echo '
	<select id="picked_classes" onChange="selChange(this.value)" >
	<option value="10">10</option>
	<option value="20">20</option>
	<option value="30">30</option>
	<option value="50">50</option>
	</select>
	<p id="gre75" style="font-size:20px"></p>
	';
}

?>

<?php

echo '
<a id="goback" class="waves-effect waves-light btn">Back</a> 
</div>';


