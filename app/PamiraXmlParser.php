<?php
namespace App;

class PamiraXmlParser {
    protected $reader;
    
    public function __construct() {
        $this->reader = new \XMLReader();
    }
    
    public function parse($xmlPath) {
        $products = [];
        $attrs = [];
        
        try {
            if(false !== $this->reader->open($xmlPath, 'utf-8')) {
                while($this->seekToElement('Element')) {
                    $attr = $this->readAllAttributes();
                    $this->reader->read();
                    $name = $this->reader->readString();
                    
                    $products[] = $name;
                    $attrs[] = $attr;
                }
            }
        } finally {
            $this->reader->close();
        }
        
        $result = new \stdClass;
        $result->products = $products;
        $result->attributes = $attrs;
        return $result;
    }
    
    protected function seekToElement($elemName) {
        while(false !== $this->reader->read()) {
            if($this->reader->nodeType == \XMLReader::ELEMENT && $this->reader->localName == $elemName) {
                return true;
            }
        }
        return false;
    }

    protected function readAllAttributes() {
        $this->reader->moveToFirstAttribute();
        $result = [ ];
        do {
            $name = $this->reader->localName;
            $value = $this->reader->hasValue ? $this->reader->value : null;
            $name = strtolower($name);
            
            $result[$name] = $value;
        } while($this->reader->moveToNextAttribute());
        
        return $result;
    }
}

