<?php

define('ROOT', dirname(__FILE__));
define('URL', "http://localhost/portailmoovci/index.php");
define('LOG_PATH', '/var/log/portailmoovci');
define('CLASSES', ROOT . '/class/');
define('INI_FILE', ROOT . '/ini/services.ini');
define('INI_MENU', ROOT . '/ini/menus.ini');
define('SESSION_FILE_DIR', LOG_PATH . '/sessionfiledir/');
define('NUMERO_NON_AUTORISE', "Desole, votre numero n'est pas autorise a utiliser ce service. Contacter le fournisseur de service SVP.");
define('SERVICE_INDISPONIBLE', "Desole le service est actuellement indisponible, Merci de reessayer plus tard.");
define('SERVICE_INDEFINI', "Desole le service n'est pas encore disponible actuellement.");
define('ERREUR_PARAMETRES', "Params absents : ");
define('ERREUR_CONNEXION_BDD', "Connexion a la base de donnes impossible ou interrompu");
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'digital');
define('DB_NAME', 'portailmoovci');
define('SIGNIFICANT', -8);
define('INDICATIF', '225');

autoload('DB|LOG');

function autoload($classes) {
    $explodeClasses = explode('|', $classes);
    for ($i = 0, $explodeClassesMax = count($explodeClasses); $i < $explodeClassesMax; $i++) {
        include_once(CLASSES . $explodeClasses[$i] . '.php');
    }
}

function db_connect() {
    $db_params = array('host' => DB_HOST, 'user' => DB_USER, 'password' => DB_PASS, 'database' => DB_NAME);
    $db = new DB($db_params);
    return $db;
}

function logger($type, $info) {
    $log = new LOG(LOG_PATH);
    $log->cdr($type, $info);
}

function select_in_table($telephone, $table) {
    $num_rows = 0;
    $dbi = db_connect();
    if ($dbi->test_connexion()) {
        $requete = "SELECT * FROM " . $table . " WHERE right(msisdn, " . abs(SIGNIFICANT) . ") = '" . substr($telephone, SIGNIFICANT) . "'";
        $exec = $dbi->db_query($requete);
        if ($exec) {
            $num_rows = count($assoc = $dbi->db_fetch_assoc($exec));
        }
        $dbi->db_close();
    }
    return $num_rows;
}

function insert_in_table($telephone, $table) {
    $dbi = db_connect();
    if ($dbi->test_connexion()) {
        $requete = "INSERT IGNORE INTO " . $table . " SET msisdn = '" . substr($telephone, SIGNIFICANT) . "'";
        $dbi->db_query($requete);
        $dbi->db_close();
    }
}

function check_for_new_url($telephone, $code, $canal) {
    $assoc = null;
    $dbi = db_connect();
    if ($dbi->test_connexion()) {
        $query = "SELECT * FROM test_app WHERE right(telephone, " . abs(SIGNIFICANT) . ") = '" . substr($telephone, SIGNIFICANT) . "' AND code = '$code' AND canal = '$canal' LIMIT 1";
        $exec = $dbi->db_query($query);
        if ($exec) {
            $assoc = $dbi->db_fetch_assoc($exec);
        }
        $dbi->db_close();
    }
    return $assoc;
}

function get_service($code) {
    $parseIniFile = parse_ini_file(INI_FILE, true);
    $serviceConfig = $parseIniFile[$code];
    return $serviceConfig;
}

function get_menu($code) {
    $parseIniFile = parse_ini_file(INI_MENU, true);
    $menuConfig = $parseIniFile[$code];
    return $menuConfig;
}

function get_repartition($repartition, $nombre) {
    $repartition_explode = explode("|", $repartition);
    foreach ($repartition_explode as $repartition_explode_value) {
        $nombre_params = explode("==", $repartition_explode_value);
        if ($nombre_params[0] == $nombre) {
            return $nombre_params[1];
        }
    }
    return null;
}

function setResponseArray($response, $headersize) {
    $tab = array('FreeFlow' => 'FC', 'next' => 'menu', 'ussdString' => '');
    $header = substr($response, 0, $headersize);
    $body = substr($response, $headersize);
    $headers = explode("\r\n", $header);
    foreach ($headers as $explodeHeader) {
        if (preg_match('/next/', $explodeHeader) or preg_match('/FreeFlow/', $explodeHeader) or preg_match('/ussdString/', $explodeHeader)) {
            list($key, $value) = explode(':', $explodeHeader, 2);
            $key = trim($key);
            $tab[$key] = trim($value);
        }
    }
    if (isset($body) && !empty($body)) {
        $search = array("<br>", "<br />", "\r\n", "\n\r", "\n");
        $replace = array("{CR}", "{CR}", "{CR}", "{CR}", "{CR}");
        $body = str_replace($search, $replace, $body);
        $tab['ussdString'] = $body;
    }
    #print_r($tab);
    return $tab;
}

function isAllowed($telephone) {
    $nums_rows = select_in_table($telephone, "blacklist");
    if ($nums_rows > 0) {
        return FALSE;
    } else {
        return TRUE;
    }
}

function afficheResponse($tab, $stk = false) {
    global $info;
	// $tab['ussdString'] = nettoyerChaine((enleverCaracteresSpeciaux($tab['ussdString'])));
	// $tab['ussdString'] = utf8_decode($tab['ussdString']);
	$tab['basename'] = $info['code'].".php";
    if(!$stk) {
		header("FreeFlow: " . $tab['FreeFlow']);
		header("next: " . $tab['next']);
		$toPrint = $tab['ussdString'];
	}
	else {
		$toPrint = "<ussdMessage><ussdString>".$tab['ussdString']."</ussdString><next>".$tab['basename']."</next><freeflow>" . $tab['FreeFlow']."</freeflow></ussdMessage>";
	}
	$tab['ussdString'] = $toPrint;
    logger(__FUNCTION__, array_merge($info, $tab));
}

function enleverCaracteresSpeciaux($text) {
    $utf8 = array(
        '/[áàâãªä]/u' => 'a',
        '/[ÁÀÂÃÄ]/u' => 'A',
        '/[ÍÌÎÏ]/u' => 'I',
        '/[íìîï]/u' => 'i',
        '/[éèêë]/u' => 'e',
        '/[ÉÈÊË]/u' => 'E',
        '/[óòôõºö]/u' => 'o',
        '/[ÓÒÔÕÖ]/u' => 'O',
        '/[úùûü]/u' => 'u',
        '/[ÚÙÛÜ]/u' => 'U',
        '/ç/' => 'c', '/Ç/' => 'C', '/ñ/' => 'n', '/Ñ/' => 'N',
        '/Œ/' => 'OE', '/œ/' => 'oe', '/æ/' => 'ae', '/Æ/' => 'AE',
        '/–/' => '-', '/[‹«]/u' => '<', '/[›»]/u' => '>', '/[“‘‚”’‚“”„"]/u' => "'", '/ /' => ' '
    );
    return trim(preg_replace(array_keys($utf8), array_values($utf8), $text));
}

function nettoyerChaine($string) {
    $dict = array("\r" => '', "\t" => ' ', '{CR}' => "\n", "\n\n" => "\n", "  " => " ", "ussdString:" => "");
    $string = str_ireplace(array_keys($dict), array_values($dict), $string);
    $string = str_ireplace("\n\n", "\n", $string);
    $string = str_ireplace("\n\n", "\n", $string);
    $string = str_ireplace("  ", " ", $string);
    $string = str_ireplace("\n", "{CR}", $string);
    return trim($string);
}

function clean($chemindossier, $nb_jour_max) {
    $nb_fichier = 0;
    if ($dossier = opendir($chemindossier)) {
        while (false !== ($fichier = readdir($dossier))) {
            if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php') {
                $filename = $chemindossier .'/'. $fichier;
		if (file_exists($filename) && is_file($filename)) {
                $dateDiff = dateDiff(@time(), filemtime($filename));
                $jours = intval($dateDiff['day']);
                if ($jours > $nb_jour_max) {
                    $commande = "rm -rf " . $filename;
                    echo $commande.PHP_EOL;
                    #exec($commande);
                    $nb_fichier++; // On incrémente le compteur de 1
                    $suppression['fichier'] = $fichier;
                    $suppression['numero'] = $nb_fichier;
                    logger(__FUNCTION__, $suppression);
                }
            }
	  }
        }
        closedir($dossier);
        $clean['dossier'] = $chemindossier;
        $clean['nombre_supprime'] = $nb_fichier;
        logger(__FUNCTION__, $clean);
    }
}

function dateDiff($date1, $date2) {
    $diff = abs($date1 - $date2); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative
    $retour = array();
    $tmp = $diff;
    $retour['second'] = $tmp % 60;
    $tmp = floor(($tmp - $retour['second']) / 60);
    $retour['minute'] = $tmp % 60;
    $tmp = floor(($tmp - $retour['minute']) / 60);
    $retour['hour'] = $tmp % 24;
    $tmp = floor(($tmp - $retour['hour']) / 24);
    $retour['day'] = $tmp;
    return $retour;
}

?>
