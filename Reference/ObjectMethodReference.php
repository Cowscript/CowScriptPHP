<?php
namespace Script\Reference;

use Script\Object\ClassContainer;
use Script\Exception\ScriptRuntimeException;
use Script\ValueHandler;
use Script\TypeHandler;
use Script\ScriptContainer;

class ObjectMethodReference implements IReference{
  private $obj;
  private $name;
  private $allowPrivate;
  private $container;
  
  public function __construct($obj, string $name, bool $allowPrivate, ScriptContainer $container){
    $this->obj          = $obj;
    $this->name         = $name;
    $this->allowPrivate = $allowPrivate;
    $this->container    = $container;
  }
  
  public function put($value){
    throw new ScriptRuntimeException("Cant assign method");
  }
  
  public function getBase(){
    return $this->obj;
  }
  
  public function getValue(){
    if($this->obj instanceof ClassContainer){
      $obj = ValueHandler::toClass($this->obj);
      if(!$obj->hasStaticMethod($this->name))
        throw new ScriptRuntimeException("Cant get static method '{$this->name}' from '{$obj->getName()}'");
      
      if(!$this->allowPrivate && !$obj->staticMethodPublic($this->name))
        throw new ScriptRuntimeException("Cant access static method '{$this->name}' from '{$obj->getName()}'");
      
      return $obj->getStaticMethod($this->name);
    }
    if($this->obj === null)
      throw new ScriptRuntimeException("Cant get the method '{$this->name}' of null");
    
    $obj = ValueHandler::toObject($this->obj, $this->container);
    
    if(!$obj->hasMethod($this->name))
      throw new ScriptRuntimeException("Cant get method '{$this->name}' from '{$obj->getName()}'");
    
    if(!$this->allowPrivate && !$obj->methodPublic($this->name))
      throw new ScriptRuntimeException("Cant access '{$this->name}' from '{$obj->getName()}'");
    
    return $obj->getMethod($this->name);
  }
}