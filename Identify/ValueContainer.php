<?php
namespace Script\Identify;

class ValueContainer{
  private $value;
  private $options = [
    "const"  => false,
    "global" => false,
    ];
  
  public function __construct($value){
     $this->value = $value;
  }
  
  public function isConst() : bool{
    return $this->options["const"];
  }
  
  public function setConst(){
    $this->options["const"] = true;
  }
  
  public function isGlobal() : bool{
    return $this->options["global"];
  }
  
  public function setGlobal(){
    $this->options["global"] = true;
  }
  
  public function getValue(){
    return $this->value;
  }
}