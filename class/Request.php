<?php

class Request {

    public function __construct($array) {
        foreach ($array as $key => $value) {
            $this->setValue($key, $value);
        }
    }

    public function getValue($name) {
        if (isset($this->{$name}) && !empty($this->{$name})) {
            return $this->{$name};
        } else {
            return null;
        }
    }

    public function setValue($name, $value) {
        $this->{$name} = $value;
    }

    public function getElements($array) {
        foreach ($array as $key => $value) {
            $elements[$key] = $this->getValue($value);
        }
        return $elements;
    }
}

?>