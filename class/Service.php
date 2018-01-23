<?php

class Service {
    
    public function __construct($iniConfig = null) {
        foreach ($iniConfig as $key => $value) {
            $this->{$key} = $value;
        }
    }

    private function check_new_url($telephone, $code, $canal) {
        $array = check_for_new_url($telephone, $code, $canal);
        $count = count($array);
        logger(__FUNCTION__, array("telephone" => $telephone, "code" => $code, 'canal' => $canal, "count" => $count));
        if ($count > 0) {
            $this->serviceLink = $array['urlORcontext'];
            logger(__FUNCTION__, array('telephone' => $telephone, 'code' => $code, 'canal' => $canal, 'URL' => $this->serviceLink));
        }
    }

    public function call($info, $canal) {
        if ($canal == "SMS" and isset($this->smsService) and ! empty($this->smsService)) {
            $this->serviceLink = $this->smsService;
        }
        $this->check_new_url($info['telephone'], $info['code'], $canal);
        $urlArrayParams = array(
            'SOA' => $info['telephone'],
            'DA' => $this->shortcode,
            'Content' => $info['message'],
            'sessionId' => $info['sessionId'],
            'next' => $info['next'],
            'canal' => $canal,
            'smscid' => $this->smscid
        );
        $urlStringParams = http_build_query($urlArrayParams);
        $url = $this->serviceLink . "?" . $urlStringParams;
        logger(__FUNCTION__, array("name" => $this->name, "url" => $url));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $this->response = trim(strip_tags(curl_exec($ch)));
        $this->headersize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
    }

    public function getResponse() {
        return $this->response;
    }

    public function getHeadersize() {
        return $this->headersize;
    }
}

?>
