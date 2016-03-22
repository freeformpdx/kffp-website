<?php
/**
 * Download file.
 */

/* Check if there is the cookie that allow the download */

if(!isset($_COOKIE["mapdownload"]) || $_COOKIE["mapdownload"] !== "true")
  die ($_COOKIE["mapdownload"].'  <b>Something goes wrong, you don\'t have permission to use this page, sorry.</b>') ;

unset($_COOKIE['mapdownload']);
setcookie('mapdownload', 'false', time() - 3600, '/');

$file_name = $_GET["filename"];
$file_url = $_GET["fileurl"];
$file_url = str_replace(" ", "%20", $file_url);

$web_root = $_SERVER["DOCUMENT_ROOT"];
$web_address = $_SERVER['HTTP_HOST'];

$pos = strrpos($file_url, $web_address);

if($pos){

  if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $protocol = 'https://';
  }
  else {
    $protocol = 'http://';
  }

  $file_url = str_replace ($protocol. $web_address .'/', '', $file_url);
  $file_url = $web_root ."/". $file_url;
  $file_url = str_replace('//', '/', $file_url);

  //die($protocol . " --- " .$web_root . " --- " .$web_address . " --- " . $file_url );

}


$filename = basename ($file_url) ;
$file_extension = strtolower (substr (strrchr ($filename, '.'), 1)) ;


function getFileSize($url) {
  if (substr($url,0,4)=='http') {
    $x = array_change_key_case(get_headers($url, 1),CASE_LOWER);
    if ( strcasecmp($x[0], 'HTTP/1.1 200 OK') != 0 ) { $x = $x['content-length'][1]; }
    else { $x = $x['content-length']; }
  }
  else { $x = @filesize($url); }
  return $x;
}

$fileSize = getFileSize($file_url);

function fileExists($path){
  return (@fopen($path,"r")==true);
}


if(!fileExists($file_url))
  die("<br> The file <b>" .$file_url. "</b> doesn't exist; check the URL");


//This will set the Content-Type to the appropriate setting for the file
switch ($file_extension)
{

  case 'mp3':
    $content_type = 'audio/mpeg' ;
    break ;
  case 'mp4a':
    $content_type = 'audio/mp4' ;
    break ;
  case 'wav':
    $content_type = 'audio/x-wav' ;
    break ;
  case 'ogg':
    $content_type = 'audio/ogg' ;
    break ;

  //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
  /*
      case 'php':
      case 'htm':
      case 'html':
      case 'txt':

      die ('<b>Cannot be used for '. $file_extension .' files!</b>') ;
      break;
  */
  default:
    die ('<b>You can\'t access '. $file_extension .' files!</b>') ;
//        $content_type = 'application/force-download' ;
}

//phpinfo();
//die("<br> - file_extension::  ". $file_extension ."<br> - content_type::  ". $content_type ."<br> - file_name::  ". $file_name ."<br> - file_url::  ". $file_url ."<br> - file size::  ". $fileSize . "<br> - curl exist::  ". function_exists('curl_version') ."<br> - allow_url_fopen::  ". ($fp=@fopen($file_url,'rb')) );

header ('Pragma: public') ;
header ('Expires: 0') ;
header ('Cache-Control: must-revalidate, post-check=0, pre-check=0') ;
header ('Cache-Control: private') ;
header ('Content-Type: ' . $content_type);
header("Content-Description: File Transfer");
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"".$filename."\"");
header('Content-Length: '.$fileSize);
header('Connection: close');

if($fp=@fopen($file_url,'rb')){
  sleep(1);
  ignore_user_abort();
  set_time_limit(0);
  while(!feof($fp))
  {
    echo (@fread($fp, 1024*8));
    ob_flush();
    flush();
  }
  fclose ($fp);

}else if(function_exists('curl_version')){
  $ch = curl_init();
  curl_setopt ($ch, CURLOPT_URL, $file_url);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
  $contents = curl_exec($ch);
  // display file
  echo $contents;
  curl_close($ch);

}else{
  // ob_end_flush();
  ob_clean();
  flush();
  @readfile ($file_url) ;
}

clearstatcache();

exit;
