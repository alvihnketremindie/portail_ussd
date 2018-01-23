<?php

class FreeFlow {

    public function __construct(FileSession $fileSession, $freeflow, $code, $shortcode, $next) {
        if ($freeflow == "FC") {
            $fileSession->writeInFile("$code|$shortcode|$next");
        } else {
            $fileSession->deleteFile();
        }
    }

}

?>