<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\Reference\ReferenceHandler;
use Script\Reference\ObjectPointerReference;
use Script\Object\ObjectContainer;
use Script\Object\ClassHandler;

class PointerExpresion implements IExpresion{
  private $base;
  private $name;
  
  public function __construct(IExpresion $base, string $name){
    $this->base = $base;
    $this->name = $name;
  }
  
  public function getValue(ScriptContainer $container){
    $base = ReferenceHandler::getValue($this->base->getValue($container));
    if($base instanceof ObjectContainer){
      return new ObjectPointerReference($base, $this->name, $this->base instanceof ThisExpresion, $container);
    }
    return new ObjectPointerReference($base, $this->name, $this->base instanceof SelfExpresion, $container);
  }
}