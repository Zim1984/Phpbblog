<?php
/***************************************************************************
 *                                blog_captcha.php
 *                            -------------------
 *   begin                : Skippy, Ap 16, 2005
 *   copyright            : (C) 2001 Cricca Hi!Wap
 *   email                : nardi@criccahiwap.it
 *
 *   $Id: blog_captcha.php,v 1.2.0 2006/02/5 12:02:15 nardi Exp $
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}


function trackback_response($error = 0, $error_message = '', $status) {
   if ($GLOBALS['method'] == 'trackback') {
	   header('Content-Type: text/xml; charset=utf-8' );
		 echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
		 echo "<response>\n";
		 $error = ($error === '' || $error == 0) ? '0' : '1';
		 echo "<error>$error</error>\n";
		 if ($data) echo "<message>$error_message</message>\n";
		 echo "</response>";
   }
   else {
      header("Status: $status");
      header("Content-Type: text/xml");
      print "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">\n<methodResponse>\n";
      if ($error !== '') {
         print "<fault><value><struct>\n";
         print "<member><name>faultCode</name><value><int>$error</int></value></member>\n";
         print "<member><name>faultString</name><value><string>$error_message</string></value></member>\n";
         print "</struct></value></fault>\n";
      }
      else {
         print "<params><param>\n";
         print "<value><string>Hi there. Your pingback has been registered.</string></value>\n";
         print "</param></params>\n";
      }
      print "</methodResponse>\n";
   }
   exit;
}





function getIP() {
  if ( !empty ( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {  
   	  $ip = $_SERVER[ 'HTTP_CLIENT_IP' ];  
  }  
  else if ( !empty ( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) {  
  	  $ip = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];  
  }  
  else if ( !empty ( $_SERVER[ 'REMOTE_ADDR' ] ) ) {  
  	  $ip = $_SERVER[ 'REMOTE_ADDR' ];  
  }  
  else if ( getenv( "HTTP_CLIENT_IP" ) ) {  
   	  $ip = getenv( "HTTP_CLIENT_IP" );  
  }  
  else if ( getenv( "HTTP_X_FORWARDED_FOR" ) ) {  
  	  $ip = getenv( "HTTP_X_FORWARDED_FOR" );  
  }  
  else if ( getenv( "REMOTE_ADDR") ) {  
  	  $ip = getenv( "REMOTE_ADDR" );  
  }  
  else {   
  	  $ip = "UNKNOWN";  
  }  
  return( $ip );  
}




/**
 * constructor Pingback
 * Costructor for Pingback class. Sends pingbacks to all links in a post
 * @param string Content of the source post
 * @param string Permalink to the source post
**/
public static function pingback_all_links( $text, $uri ) {
  preg_match_all( "@<a(?:\s+[^>]*)?\s+href=([\"'])(https?://.+?)\\1[^>]*>(?:.*?)</a>@is", $text, $matches );
  $links= array_unique( $matches[2] ); //Remove duplicate entries
  foreach ( $links as $link ) {
    send_pingback( $link, $uri );
  }
}



/**
 * private function send_pingback
 * Sends pingback to a specific URI
 * @param string URI to pingback
 * @param string Permalink to the source post
**/
function trackback_send(&$message, $title, $trackback_url, $blog_url, $blog_name, $excerpt) {

        // rawurlencode($excerpt);??????
$data = "title=".urlencode($title)."&url=".urlencode($blog_url)."&blog_name=".urlencode($blog_name)."&excerpt=".urlencode($excerpt);

$host = parse_url($trackback_url, '2');
$port = parse_url($trackback_url, '3');
$trackback_uri = parse_url($trackback_url, '6') . parse_url($trackback_url, '7');

//set the port
if (!is_numeric($port)) $port = 80;
     
//connect to the remote-host  
$fp = @fsockopen($host, $port);
//$fp = @fsockopen($host, 80);

@fputs($fp, "POST ".$trackback_url." HTTP/1.1\r\n");
@fputs($fp, "Host: ".$host."\r\n");
@fputs($fp, "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n");
@fputs($fp, "Content-length: ".strlen($data)."\r\n");
@fputs($fp, "Connection: close\r\n\r\n");
@fputs($fp, $data);


//  Send the header and get the results, saved in $response
$response = @stream_get_contents($fp);
//  Always close for good measure
//fclose($fp);
@fclose($fp); 


//  If this is true, there was no error
if (stripos($response, '<error>0</error>'))
    $message = "All clear! Trackback was sent successfully.";
    return false;
else
    {
    //  Find the beginning and end of the message element
    $start_resp = stripos($response, '<message>');
    $end_resp = stripos($response, '</message>');
 
    //  substr will return the actual message element, 
    //    with the tags included
    $message = substr($response, $start_resp, 
        $end_resp - $start_resp - 1);
 
    //  Replace the tags with blank space
    $message = str_replace('<message>', '', $message);
    $message = str_replace('</message>', '', $message);
    $message = "Error: " . $outcome;
    return true;
    }
}

/*
$pingresult = array();
$pingresult[] = doPing("rpc.weblogs.com","/RPC2");
$pingresult[] = doPing("rpc.technorati.com","/rpc/ping");
$pingresult[] = doPing("xmlrpc.blogg.de","/");
echo join($pingresult);*/
function doPing($host, $path) {
  $blog_title = "Mein schönes Blog";
  $blog_url = "http://www.meinschoenesblog.de";
  $timeout = 30;         //Sekunden
  
  $fp = fsockopen($host, 80, $errno, $errstr, $timeout);
    if(!$fp) { 
      $response = 'Fehler: '.$errstr.' ('.$errno.')<br />Es konnte keine Verbindung hergestellt werden';
    } else {
      $data_string = '<?xml version="1.0" encoding="iso-8859-1"?'.'>
      <methodCall>
       <methodName>weblogUpdates.ping</methodName>
        <params>
         <param><value>'.$blog_title.'</value></param>
         <param><value>'.$blog_url.'</value></param>
       </params>
      </methodCall>';
      $data_header = "POST $path HTTP/1.0\r\n".
      "Host: $host\r\n".
      "Content-Type: text/xml\r\n".
      "User-Agent: qxm XML-RPC Client\r\n".
      "Content-Length: ".strlen($data_string)."\r\n\r\n";
      fputs($fp, $data_header);
      fputs($fp, $data_string);
      $response = '';
      while(!feof($fp)) $response.=fgets($fp,2048);
      fclose($fp);
      $response = preg_replace("/.*<\/boolean>/si","",$response);
      $response = preg_replace("/.*/si","",$response);
      $response = preg_replace("/<\/value>.*/si","",$response);
    }
  return '<h2>'.$host.'</h2><p>'.$response.'</p>';
}



/**
 * private function send_pingback
 * Sends pingback to a specific URI
 * @param string URI to pingback
 * @param string Permalink to the source post
**/
private static function send_pingback( $target_uri, $post_uri ) {
  $rr= new RemoteRequest( $target_uri );
  if ( ! $rr->execute() ) {
    // request errored out
    return;
  }

  $headers= $rr->get_response_headers();
  $body= $rr->get_response_body();

  if ( preg_match( '/^X-Pingback: (\S*)/mi', $headers, $matches ) ) {
    // If remote sends an X-Pingback header, use the URI specified
    $pingback_endpoint= $matches[1];
  }
  elseif ( preg_match( '@<link rel="pingback" href="([^"]+)" ?/?>@si', $body, $matches ) ) {
    // If there is a <link> element with a rel of "pingback"
    $pingback_endpoint= $matches[1];
  }
  else {
    // no pingback facility found
    return;
  }
  
  $pingback= new RPCClient( $pingback_endpoint, 'pingback.ping', array( $post_uri, $target_uri ) );
  $pingback->execute();
}

?>