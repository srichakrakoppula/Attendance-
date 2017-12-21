<?php
// init the resource
$ch = curl_init();

// set a single option...
//curl_setopt($ch, OPTION, $value);

$postData = array(
    'txtLoginID' => '1602-12-737-051	',
    'txtPWD' => 'easier',
    'redirect_to' => 'http://www.vce.ac.in/student_info.aspx'
    //'testcookie' => '1'
);


// ... or an array of options
curl_setopt_array(
    $ch, array( 
    CURLOPT_URL => 'http://www.vce.ac.in',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData,
    CURLOPT_FOLLOWLOCATION => true
));

// execute
$output = curl_exec($ch);

// free
curl_close($ch);

echo $output;

?>