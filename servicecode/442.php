<?php

$arrayDefault = array(
    "telephone" => "msisdn",
    "sessionId" => "sessionId",
    "message" => "input",
    "code" => "code"
);
ERROR_REPORTING(E_ALL);
header('Content-type:text/plain;charset=UTF-8');
include_once ('../global.php');
$code = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
autoload('Request|FileSession|FreeFlow|Service');
$_REQUEST['code'] = $code;
$request = new Request($_REQUEST);
$info = $request->getElements($arrayDefault);
$ussdservicecode = $info['code'];
$message = $info['message'];
$sessionid = "service-" . $ussdservicecode . "-" . $info['sessionId'];
$fileSession = new FileSession($sessionid);
$serviceConfig = get_service($ussdservicecode);
if (isset($serviceConfig) and ! empty($serviceConfig)) {
    if (!$fileSession->ifFileExists()) {
        $shortcode = $serviceConfig['shortcode'];
        $syntaxe = "*" . $shortcode . "#";
        $next = $serviceConfig['next'];
    } else {
        list($syntaxe, $shortcode, $next) = explode('|', $fileSession->getFileContent());
    }
    if (isAllowed($info['telephone'])) {
        $info['next'] = $next;
        $service = new Service($serviceConfig);
        $service->call($info, "USSD");
        $responseArray = setResponseArray($service->getResponse(), $service->getHeadersize());
    } else {
        $responseArray = array('next' => 'menu', 'FreeFlow' => 'FB', 'ussdString' => NUMERO_NON_AUTORISE);
    }
} else {
    $responseArray = array('next' => 'menu', 'FreeFlow' => 'FB', 'ussdString' => SERVICE_INDEFINI);
    logger('noServiceFound', array_merge($info, array("code" => "$code")));
}

$freeFlow = new FreeFlow($fileSession, trim($responseArray['FreeFlow']), $syntaxe, $shortcode, trim($responseArray['next']));
afficheResponse($responseArray);
?>
