<?php
header('Content-type:text/plain;charset=UTF-8');
include_once (dirname(__FILE__) . '/global.php');
autoload('Request');
$request = new Request($_REQUEST);
$sessionid = $request->getSessionId();
$shortcode = $request->getDA();
$next = $request->getNext();
$info = $request->getElements();
$serviceConfig = get_service($shortcode);
if (isset($serviceConfig) and ! empty($serviceConfig)) {
    autoload('Service');
    $service = new Service($serviceConfig);
    $service->call($info, "SMS");
}
?>
