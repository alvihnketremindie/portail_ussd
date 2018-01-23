<?php

$dbi = mysqli_connect('localhost', 'root', 'digital', 'portailairtelcongob');
if ($dbi) {
    $i = 0;
    print "Connexion a la BD" . PHP_EOL;
    $file = $_SERVER['argv'][1];
    if ($fp = @fopen($file, 'r')) {
        print "Ouverture du fichier $file" . PHP_EOL;
        while (!feof($fp)) {
            $line = trim(fgets($fp, 4096));
            if ($line != "") {
                $i++;
                $numero = trim($line);
                $query = "INSERT IGNORE INTO postpaid VALUE ('', '" . escape($numero) . "')";
                mysqli_query($dbi, $query) or die(mysqli_error($dbi));
                $affectedRows = mysqli_affected_rows($dbi);
                print "($i, $query, $affectedRows)" . PHP_EOL;
            }
        }
    }
    mysqli_close($dbi);
    print('Nombre de lignes insérées: ' . $i . PHP_EOL);
}

function escape($value) {
    global $dbi;
    return mysqli_real_escape_string($dbi, $value);
}

?>
