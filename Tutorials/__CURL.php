<?php

$url = "www.cleanroom-solutions.com";

$optList = array(
	'CURLOPT_HEADER'=>array('v'=>1,'a'=>1),
	'CURLOPT_NOPROGRESS'=>array('v'=>1,'a'=>0),
	'CURLOPT_NOBODY'=>array('v'=>1,'a'=>1),
	'CURLOPT_RETURNTRANSFER'=>array('v'=>1,'a'=>1),
	'CURLOPT_FOLLOWLOCATION'=>array('v'=>1,'a'=>1),
	'CURLOPT_VERBOSE'=>array('v'=>1,'a'=>0),
	'CURLOPT_MAXCONNECTS'=>array('v'=>5,'a'=>1),
	'CURLOPT_TIMEOUT'=>array('v'=>10,'a'=>1),
	'CURLOPT_USERAGENT'=>array('v'=>'spider','a'=>0),
	'CURLOPT_AUTOREFERER'=>array('v'=>true,'a'=>0),
	'CURLOPT_REFERER'=>array('v'=>1,'a'=>'http://www.myinfo.ie'),
	'CURLOPT_COOKIEJAR'=>array('v'=>'kookie.txt','a'=>0),
	'CURLOPT_COOKIEFILE'=>array('v'=>'kookie.txt','a'=>0),
	'CURLOPT_IPRESOLVE'=>array('v'=>'CURL_IPRESOLVE_V4','a'=>0)
);
echo "<h1>cURL check</h1>";

if (isset($_POST['action']) && $_POST['action']=="submitted" && $_POST['url']) {
	$optList['CURLOPT_URL'] = array('v'=>'','a'=>0);
	//-------------------------------------------------------------------------------------------
	echo "<h2>1. cURL load check</h2>";
	if (!extension_loaded('cURL')) {
		echo "<h2>cURL not loaded</h2>";
	    if (dl('cURL.so')) {
	        echo "<h4>cURL now loaded</h4>";
	    } else {
	    	echo "<h4>cURL would not load</h4>";
	    }
	} else {
		echo "<h4>cURL already in place</h4>";
	}
	//-------------------------------------------------------------------------------------------	echo "<hr />";
	echo "<h2>2. cURL version and constants check</h2>";
	$version = curl_version();
	// These are the bitfields that can be used
	// to check for features in the curl build
	$bitfields = array(
		'CURL_VERSION_IPV6',
		'CURL_IPRESOLVE_V4',
		'CURL_IPRESOLVE_V6',
		'CURLOPT_IPRESOLVE'
	);
	foreach($bitfields as $feature) {
		echo "<li><b>".@constant($feature).": </b>".$feature.($version['features'] & @constant($feature) ? " matches" : " does not match")."</li>";
	}
	echo "<p><b>Debug:</b>".print_r($version,true)."</p>";
	//-------------------------------------------------------------------------------------------
	echo "<h2>3. cURL Parameters Used</h2>";
	$url = substr($_POST['url'],0,7)=="http://"? $_POST['url']: "http://".$_POST['url'];
	$ctl = $_POST['ctl'];
	echo "<ul>";
	foreach ($optList as $name=>$arr){
		if (in_array($name,array('CURLOPT_HEADER','CURLOPT_NOPROGRESS','CURLOPT_NOBODY','CURLOPT_RETURNTRANSFER',
			'CURLOPT_FOLLOWLOCATION','CURLOPT_VERBOSE'))){
			$options[constant($name)] = isset($ctl[$name])? $ctl[$name]: 0;
			echo "<li>".$name." -> ".$options[constant($name)]."</li>";
		} else {
			if ($ctl[$name]){
				$options[constant($name)] = $ctl[$name];
				echo "<li>".$name." -> ".$options[constant($name)]."</li>";
			}
		}
	}
	echo "</ul>";
	//-------------------------------------------------------------------------------------------
	echo "<h2>4. Initialise cURL</h2>";
	echo "<h4>CURLOPT_URL -> ".$url."</h4>";
	$ch = curl_init();
	curl_setopt_array($ch, $options);
	curl_setopt ($ch, CURLOPT_URL, $url);
	$html = curl_exec($ch);
	$err = curl_error($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
    if ($err){
    	echo "<h3>ERROR: ".$err."</h3>";
    } else {
    	echo "<p>No error log found</p>";
    }
    echo "<p><b>Debug:</b>".print_r($info,true)."</p>";
	//-------------------------------------------------------------------------------------------
	echo "<h2>5. cURL Results</h2>";
	echo "<p>";
	if ($err){
		echo "<p><b>Error:</b> nothing to show</p>";
	} else {
		echo "<p>".htmlentities($html)."</p>";
	}
	echo "</p>";
	echo "<hr />";
	//-------------------------------------------------------------------------------------------
	echo "<p><a href='".$_SERVER['PHP_SELF']."'>Try again</a></p>";
	echo "<hr />";
	//phpinfo();
} else {
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype=''>";
	echo "<table>";
	echo "<tr><td>Page URL: http://</td>";
	echo "<td><input type='text' name='url' value='".$url."' size='40' maxlength='90'/></td>";
	echo "</tr>";
	echo "<tr><td colspan='2'><u>Controls</u><br />";
	foreach($optList as $name=>$arr) {
		$tick = $arr['a']? "checked='checked'": "";
		$zz = $arr['v']>1? "(".$arr['v'].")": "";
		echo "<input type='checkbox' name='ctl[".$name."]' value='".$arr['v']."' ".$tick." />".$name." ".$zz."<br />";
	}
	echo "</td></tr>";
	echo "<tr><td colspan='2'><input type='hidden' name='action' value='submitted' />";
	echo "<input type='submit' name='submit' value='Submit' />";
	echo "<input type=button onClick=\"location.href='".$_PHP_SELF."'\" value='Cancel' /></td></tr>";
	echo "</table>";
	echo "</form>";
}

?>