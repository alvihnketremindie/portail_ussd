<?php

class LOG {

    var $chemin;

    function __construct($chemin) {
        $this->chemin = $chemin;
    }

    public function getLog($array) {
        $toReturn = '';
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $toReturn .= $key . ' => (' . parse_reponse($value).')';
                } else {
                    $toReturn .= ' | ' . $key . '=' . $value;
                }
            }
        } else {
            $toReturn .= ' | ' . $array;
        }
        return $toReturn;
    }

    function cdr($action, $to_log) {
        $log_date = @date("Y-m-d H:i:s");
        $log_jour = @date("Ymd");
        $log_text = $this->getLog($to_log);
        $aLogger = "$log_date " . " __" . $action . "__  " . $log_text . PHP_EOL;
        $log_chemin = $this->chemin . "/" . $log_jour;
        $log_chemin_fichier = $log_chemin . ".log";
        @file_put_contents($log_chemin_fichier, $aLogger, FILE_APPEND);
        // echo $aLogger."</br>";
    }

}

?>