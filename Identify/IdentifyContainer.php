<?php
namespace Script\Identify;

use Script\Exception\ScriptRuntimeException;

class IdentifyContainer{
  private $identifys = [];
  private $owner;
  
  public function __construct($owner = null){
    $this->owner = $owner;
  }
  
  public function hasOwner() : bool{
    return $this->owner != null;
  }
  
  public function getOwner(){
    return $this->owner;
  }
  
  public function hasIdentify(string $name) : bool{
    return array_key_exists($name, $this->identifys);
  }
  
  public function pushIdentify(string $name, $value){
    $this->identifys[$name] = new ValueContainer($value);
  }
  
  public function getInformation(string $name) : ValueContainer{
    if(!$this->hasIdentify($name))
      throw new ScriptRuntimeException("Unknown identify: ".$name);
    
    return $this->identifys[$name];
  }
}