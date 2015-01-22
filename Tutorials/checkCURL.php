<?php
/*
 * Created on 7 Dec 2009    by Clinton
 *
 * Simple form template
 */
class Test {

    public $headers;
    //...

    public function exec($opts){
        $this->headers = array();
        $opts[CURLOPT_HEADERFUNCTION] = array($this, '_setHeader');
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        return curl_exec($ch);
    }

    private function _setHeader($ch, $header){
        $this->headers[] = $header;
        return strlen($header);
    }
}


echo "<h1>Sample Form</h1>";
if (isset($_POST['action']) && $_POST['action']=="submitted" && $_POST['url']) {
	echo "<h3>Get Site Headers</h3>";
	$url = substr($_POST['url'],0,4)=='http'? $_POST['url']: 'http://'.$_POST['url']; //assume http if not supplied
	$test = new Test();
	$opts = array(
		CURLOPT_URL => $url,	// set the URL
		CURLOPT_RETURNTRANSFER => true,			// return web page
		CURLOPT_HEADER         => true,		// don't return headers
		CURLOPT_FOLLOWLOCATION => true,			// follow redirects
		//CURLOPT_ENCODING       => "",			// handle all encodings
		//CURLOPT_USERAGENT      => "spider",	// who am i
		//CURLOPT_AUTOREFERER    => true,		// set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 120,			// timeout on connect
		CURLOPT_TIMEOUT        => 120,			// timeout on response
		CURLOPT_MAXREDIRS      => 10,			// stop after 10 redirects
		//CURLOPT_POST            => 1,			// i am sending post data
		//CURLOPT_POSTFIELDS     => $curl_data,	// this are my post vars
		//CURLOPT_SSL_VERIFYHOST => 0,			// don't verify ssl
		//CURLOPT_SSL_VERIFYPEER => false,		//
		//CURLOPT_VERBOSE        => 1			//
	);
	$data = $test->exec($opts);

	echo "<h5>The URL is <a href='".$url_build."'>".$url."</a></h5>";
	echo "<p style='color: red; margin-left: 20px'>".print_r($test->headers,true)."</p>";
	echo "<p><a href='".$_SERVER['PHP_SELF']."'>Try again</a></p>";
	echo "<hr />";
	phpinfo();
} else {
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype=''>" .
		"<table border='0' width='500px'>" .
		"<tr><td width='20%'>Enter URL: </td>" .
		"<td width='80%'>http://<input type='text' name='url' value='www.myinfo.ie' size='40' maxlength='40'/></td>" .
		"</tr>" .
		"<tr><td colspan='2'><input type='hidden' name='action' value='submitted' />" .
		"<input type='submit' name='submit' value='Submit' /> " .
		"<input type='button' value='Cancel' onclick='history.go(-1)' />" .
		"<input type=button onClick=\"location.href='".$_PHP_SELF."'\" value='Cancel' />" .
		"<input type='reset' value='Reset' /></td></tr>" .
		"</table>" .
		"</form>";
}
?>

