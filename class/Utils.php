<?php

class Utils {

    var $msisdn;
    var $sessionid;
    var $input;
    var $code;
    var $next;

    public function __construct($request, $code = null) {
        $this->setmsisdn(@$request['msisdn']);
        $this->setsessionid(@$request['sessionId'], @$request['imsi']);
        $this->setinput(@$request['input']);
        $this->setcode($code);
    }

    public function getmsisdn() {
        return $this->msisdn;
    }

    public function setmsisdn($value) {
        $msisdn = isset($value) ? $value : 'NoMsisdn';
        $this->msisdn = $msisdn;
    }

    public function getcode() {
        return $this->code;
    }

    public function setcode($value) {
        $this->code = $value;
    }

    public function getinput() {
        return $this->input;
    }

    public function setinput($value) {
        $msg = isset($value) ? $value : '';
        $this->input = $msg;
    }

    public function getsessionid() {
        return $this->sessionid;
    }

    public function setsessionid($value, $imsi = null) {
        $sessionid = isset($value) ? $value : 'NoSessionId';
        $imsi = isset($value) ? $value : "NoImsi";
        $this->sessionid = $sessionid . "_" . $imsi;
    }

    public function getnext() {
        return $this->next;
    }

    public function setnext($value) {
        $msg = isset($value) ? $value : 'menu';
        $this->next = $msg;
    }

    public function getElements() {
        $elements = array(
            "msisdn" => $this->getmsisdn(),
            'code' => $this->getcode(),
            'input' => $this->getinput(),
            'sessionid' => $this->getsessionid(),
            'next' => $this->getnext()
        );
        return $elements;
    }

}

?>
