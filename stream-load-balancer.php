<?php
$streams = array(
  'http://74.63.72.20:8950/live',
  'http://104.236.186.233:8000/stream'
);

$rand = rand(0,1);
$stream_url = $streams[$rand];

header("Location: " . $stream_url);

exit();
?>