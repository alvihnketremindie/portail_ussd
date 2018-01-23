<?php
header('Content-type:text/plain;charset=UTF-8');
include_once ('../global.php');
autoload('Request|Service');
$arrayDefault = array(
    "telephone" => "SOA",
    "sessionId" => "sessionId",
    "message" => "Content",
    "code" => "DA",
    "next" => "next"
);
$request = new Request($_REQUEST);
$info = $request->getElements($arrayDefault);
$shortcode = $info['code'];
$serviceConfig = get_service($shortcode);
if (isset($serviceConfig) and ! empty($serviceConfig)) {
    $service = new Service($serviceConfig);
    $service->call($info,"USSD");
    $responseArray = setResponseArray($service->getResponse(), $service->getHeadersize());
} else {
    $responseArray = array('next' => 'menu', 'FreeFlow' => 'FB', 'ussdString' => SERVICE_INDEFINI);
    $service = new Service();
}
afficheResponse($responseArray, true);
?>
