<?php

class FileSession {

    protected $_filePath;

    function __construct($sessionId) {
        $this->setFilePath(SESSION_FILE_DIR . $sessionId . '.log');
    }

    public function writeInFile($fileContent) {
        file_put_contents($this->_filePath, $fileContent);
    }

    public function getFileContent() {
        return file_get_contents($this->_filePath);
    }

    public function getFilePath() {
        return $this->_filePath;
    }

    public function setFilePath($file) {
        $this->_filePath = $file;
    }

    public function deleteFile() {
        exec('rm -rf ' . $this->_filePath);
    }

    public function ifFileExists() {
        if (file_exists($this->_filePath)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>
