<?php
namespace Script\Reference;

use Script\Object\ObjectContainer;
use Script\Object\ClassContainer;
use Script\Exception\ScriptRuntimeException;
use Script\ScriptContainer;

class ObjectPointerReference implements IReference{
  private $base;
  private $name;
  private $allowPrivate;
  private $container;
  
  public function __construct($obj, string $name, bool $allowPrivate, ScriptContainer $container){
    $this->base         = $obj;
    $this->name         = $name;
    $this->allowPrivate = $allowPrivate;
    $this->container    = $container;
  }
  
  public function put($value){
    if($this->base instanceof ClassContainer){
      if(!$this->base->hasStaticPointer($this->name)){
        throw new ScriptRuntimeException("Unknown static pointer '{$this->name}' of '{$this->base->getName()}'");
      }
      
      if(!$this->allowPrivate && !$this->base->isStaticPointerPublic($this->name))
        throw new ScriptRuntimeExpresion("Cant access the static pointer '{$this->name}' of ".$this->base->getName());
      
      $this->base->putStaticPointer($this->name, $value);
      return;
    }
    
    if(!$this->base->hasPointer($this->name))
      throw new ScriptRuntimeException("Unknown pointer '{$this->name}' of ".$this->base->getName());
    
    if(!$this->allowPrivate && !$this->base->isPointerPublic($this->name))
      throw new ScriptRuntimeException("Cant access the pointer '{$this->name}' of ".$this->base->getName());
    
    $this->base->putPointer($this->name, $value);
  }
  
  public function getValue(){
    if($this->base instanceof ClassContainer){
      if(!$this->base->hasStaticPointer($this->name)){
        throw new ScriptRuntimeException("Unknown static pointer '{$this->name}' of '{$this->base->getName()}'");
      }
      
      if(!$this->allowPrivate && !$this->base->isStaticPointerPublic($this->name))
        throw new ScriptRuntimeExpresion("Cant access the static pointer '{$this->name}' of ".$this->base->getName());
      
      return $this->base->getStaticPointer($this->name, $this->container);
    }
    
    if(!$this->base->hasPointer($this->name))
      throw new ScriptRuntimeException("Unknown pointer '{$this->name}' of ".$this->base->getName());
    
    if(!$this->allowPrivate && !$this->base->isPointerPublic($this->name))
      throw new ScriptRuntimeException("Cant access the pointer '{$this->name}' of ".$this->base->getName());
    
    return $this->base->getPointer($this->name, $this->container);
  }
}