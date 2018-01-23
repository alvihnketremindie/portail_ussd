<?php
// curl -v "http://localhost/portailairtelbf/servicecode/portaileco.php?SOA=0022665968103&Content=310%2A1&sessionId=ussdgw-14679114808804998&sequence=0&DA=310&next=menu&canal=USSD&DA=310&smscid=AIRTEL_310"
$arrayDefault = array(
    "telephone" => "SOA",
    "sessionId" => "sessionId",
    "message" => "Content",
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
$msisdn = $info['telephone'];
$ussdservicecode = $info['code'];
$message = $info['message'];
$sessionid = "menu-" . $ussdservicecode . "-" . $info['sessionId'];
$fileSession = new FileSession($sessionid);
$menuConfig = get_menu($ussdservicecode);
$shortcode = $menuConfig['shortcode'];
$next = 'menu';
$level = '0';
$returnMenu = false;
if (isset($menuConfig) and ! empty($menuConfig)) {
    if (preg_match("/" . $shortcode . "/i", $message)) {
        $contents = explode('*', $message);
        $level = isset($contents[1]) ? $contents[1] : '0';
        if ($level == '0') {
            $returnMenu = true;
        }
        $titre = "syntaxeDirect";
    } elseif (!$fileSession->ifFileExists()) {
        $returnMenu = true;
        $titre = "sessionFileDoNotEsxist";
    } else {
        list($level, $syntaxe, $next) = explode('|', $fileSession->getFileContent());
        if ($level == '0' and ctype_digit(trim($message))) {
            $level = trim($message);
        }
        $titre = "syntaxeNormale";
    }
    logger($titre, array_merge($info, array("level" => $level, "syntaxe" => $message)));
    if ($returnMenu) {
        $responseArray = array('next' => $next, 'FreeFlow' => 'FC', 'ussdString' => $menuConfig['libelle']);
    } else {
        $code_service_repartion = get_repartition($menuConfig['repartition'], $level);
        $serviceConfig = get_service($code_service_repartion);
        if (isset($serviceConfig) and ! empty($serviceConfig)) {
            $service = new Service($serviceConfig);
            if (isAllowed($info['telephone'])) {
                $info['next'] = $next;
                $service->call($info , "USSD");
                $responseArray = setResponseArray($service->getResponse(), $service->getHeadersize());
            } else {
                $responseArray = array('next' => 'menu', 'FreeFlow' => 'FB', 'ussdString' => NUMERO_NON_AUTORISE);
            }
        } else {
            $responseArray = array('next' => 'menu', 'FreeFlow' => 'FC', 'ussdString' => $menuConfig['libelle']);
        }
    }
} else {
    $responseArray = array('next' => 'menu', 'FreeFlow' => 'FB', 'ussdString' => SERVICE_INDEFINI);
    logger('noServiceFound', array_merge($info, array("code" => "$code")));
}

$freeFlow = new FreeFlow($fileSession, trim($responseArray['FreeFlow']), $level, $shortcode, trim($responseArray['next']));
afficheResponse($responseArray);
?>
