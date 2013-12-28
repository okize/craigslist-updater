<?php

// turn off error reporting
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  // data from front-end
  $data = json_decode(file_get_contents('php://input'), true);

  // json file
  $file = 'log.json';

  // if file doesn't exist, create it
  if (!file_exists($file)) {
      $fh = fopen($file, 'w') or die('can\'t open file');
    fclose($fh);
  }

  // array for log update
  $update = array(
    'title' => $data['title'], // post title
    'url' => $data['url'], // post url
    'dateStamp' => $data['dateStamp'] // post date
  );

  // open the json file & decode
  $json = json_decode( file_get_contents($file), true);

  // if log is empty just make $json an empty array
  if (empty($json)) {
      $json = array();
  }

  // add update to begining of array
  array_unshift($json, $update);

  // open json file and rewrite w/ new info
  $fp = fopen($file, 'w');
  fwrite($fp, json_encode(array_values($json)));
  fclose($fp);

  $response[status] = 'success';
  $response[msg] = 'update logged';

}

else {
  $response[status] = 'error';
  $response[msg] = 'nothing logged';
}

echo json_encode($response);

?>
