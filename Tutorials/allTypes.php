<?php
/**
 * Project: Tutorials package_name (allTypes.php)
 * Copyright: (C) 2013 Clinton
 * Developer:  Clinton
 * Created on 16 Dec 2013
 *
 * Description: see SEOTools/ckeditor/kcfinder/core/browser.php line 604
 *
 * This program is the property of the developer and is not intended for distribution. However, if the
 * program is distributed for ANY reason whatsoever, this distribution is WITHOUT ANY WARRANTEE or even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

echo "<h1>Get KCFinder version</h1>";
echo "<p>Demonstration of the different file loaders</p>";

$protocol = "http";
$host = "kcfinder.sunhater.com";
$port = 80;
$path = "/checkVersion.php";

$url = "$protocol://$host:$port$path";
$pattern = '/^\d+\.\d+$/';
$responsePattern = '/^[A-Z]+\/\d+\.\d+\s+\d+\s+OK\s*([a-zA-Z0-9\-]+\:\s*[^\n]*\n)*\s*(.*)\s*$/';

// file_get_contents()
if (ini_get("allow_url_fopen") &&
    (false !== ($ver = file_get_contents($url))) &&
    preg_match($pattern, $ver)

// HTTP extension
) {} elseif (
    function_exists("http_get") &&
    (false !== ($ver = @http_get($url))) &&
    (
        (
            preg_match($responsePattern, $ver, $match) &&
            false !== ($ver = $match[2])
        ) || true
    ) &&
    preg_match($pattern, $ver)

// Curl extension
) {} elseif (
    function_exists("curl_init") &&
    (false !== ($curl = @curl_init($url))) &&
    (@ob_start() || (@curl_close($curl) && false)) &&
    (@curl_exec($curl) || (@curl_close($curl) && false)) &&
    ((false !== ($ver = @ob_get_clean() )) ||  (@curl_close($curl) && false)) &&
    (@curl_close($curl) || true ) &&
    preg_match($pattern, $ver)

// Socket extension
) {} elseif (function_exists('socket_create')) {
    $cmd =
        "GET $path " . strtoupper($protocol) . "/1.1\r\n" .
        "Host: $host\r\n" .
        "Connection: Close\r\n\r\n";

    if ((false !== (  $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP)  )) &&
        (false !==    @socket_connect($socket, $host, $port)                    ) &&
        (false !==    @socket_write($socket, $cmd, strlen($cmd))                ) &&
        (false !== (  $ver = @socket_read($socket, 2048)                       )) &&
        preg_match($responsePattern, $ver, $match)
    )
        $ver = $match[2];

    if (isset($socket) && is_resource($socket))
        @socket_close($socket);
}
echo "<h4>Version: ".$ver."</h4>";

?>
