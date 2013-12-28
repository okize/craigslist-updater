<?php

// turn off error reporting
error_reporting(0);

// simple function for fuzzy dates
function timesince( $tsmp ) {

  $diffu = array(  'seconds'=>2, 'minutes' => 120, 'hours' => 7200, 'days' => 172800, 'months' => 5259487,  'years' =>  63113851 );
  $diff = time() - $tsmp;
  $dt = '0 seconds ago';
  foreach($diffu as $u => $n){ if($diff>$n) {$dt = floor($diff/(.5*$n)).' '.$u.' ago';} }
  return $dt;

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $config = parse_ini_file('config.ini');
  $json = json_decode(file_get_contents('php://input'), true); // get json from post
  $timeStamp = date('F j, Y \@ g:ia');
  $postDate = strtotime(stripslashes($json['dateStamp']));
  $postTitle = stripslashes($json['title']);
  $postUrl = stripslashes($json['url']);
  $postContent = stripslashes($json['content']);
  $fromName = $config['fromName']);
  $fromEmail = $config['fromEmail']);
  $toEmail = $config['toEmail']);
  $toSubject = $postTitle;
  $toMessage = '<h1><a href="' . $postUrl . '">' . $postTitle . '</a></h1>' .
               '<p><em>Posted to Craigslist on <strong>' . date('F jS\, Y \@ g:ia', $postDate) . '</strong>&nbsp;(about ' . timesince($postDate) . ')</em></p>' .
               '<br><br>' . $postContent;
  $emailHeader  = 'MIME-Version: 1.0' . "\r\n";   // set MIME & Content-type header
  $emailHeader .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  $emailHeader .= 'From: '. $fromName . ' <' . $fromEmail . '>\r\n';
  $toPhoneNumber = $config['toPhoneNumber'];
  $toPhoneProvider = $config['toPhoneProvider'];
  $toPhone = $toPhoneNumber . '@' . $toPhoneProvider;
  $toPhoneSubject = '';
  $toPhoneMessage = substr(date('m\/d\/Y\@g:ia', $postDate) . ' ~ ' . $postTitle, 0, 130); // truncate to < 140 chars if necessary
  $phoneHeader  = 'MIME-Version: 1.0' . "\r\n";   // set MIME & Content-type header
  $phoneHeader .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  $phoneHeader .= 'From: CL <cl@cl.org>\r\n';

  print($toPhone);

  // send posting to email addresses or show error on failure
  if (mail($toEmail, $toSubject, $toMessage, $emailHeader)) {
    $response[status] = 'success';
    $response[msg] = 'Email was sent to ' . $toEmail . ' on '. $timeStamp . '.';
  }

  if (mail($toPhone, '', $toPhoneMessage, $phoneHeader)) {
    $response[textMsg] = 'Text message was sent to ' . $toPhone . ' on '. $timeStamp . '.';
    $response[contents] = $toPhoneMessage;
  }

  else {
    $response[status] = 'error';
    $response[msg] = 'Couldn\'t send mail.';
  }

}

else {
  $response[status] = 'error';
  $response[msg] = 'no data sent';
}

echo json_encode($response);

?>