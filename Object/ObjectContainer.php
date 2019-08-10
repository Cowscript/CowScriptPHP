<?php
namespace Script\Object;

use Script\SCallable\CallableMethod;
use Script\ScriptContainer;

class ObjectContainer{
  private $owner;
  private $pointers = [];
  
  public function __construct(ClassContainer $owner){
    $this->owner = $owner;
  }
  
  public function getName() : string{
    return $this->owner->getName();
  }
  
  public function is(string $name){
    return $this->owner->is($name);
  }
  
  public function hasPointer(string $name) : bool{
    return array_key_exists($name, $this->pointers) || $this->owner->hasPointer($name);
  }
  
  public function putPointer(string $name, $value){
    $this->pointers[$name] = $value;
  }
  
  public function getPointer(string $name, ScriptContainer $container){
    if(array_key_exists($name, $this->pointers))
      return $this->pointers[$name];
    
    $value = $this->owner->getPointer($name, $container);
    $this->pointers[$name] = $value;
    return $value;
  }
  
  public function hasMethod(string $name) : bool{
    return $this->owner->hasMethod($name);
  }
  
  public function getMethod(string $name) : CallableMethod{
    return $this->owner->getMethod($name);
  }
  
  public function methodPublic(string $name) : bool{
    return $this->owner->methodPublic($name);
  }
}