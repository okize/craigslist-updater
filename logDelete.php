<?php

// turn off error reporting
error_reporting(0);

// json file
$file = 'log.json';

// if file doesn't exist, mention that, otherwise delete it
if (!file_exists($file)) {
  echo 'No log file found';
} else {
  unlink($file);
  echo 'Log file deleted';
}

?>