<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\Reference\ReferenceHandler;
use Script\Object\ObjectContainer;
use Script\Reference\ObjectMethodReference;
use Script\TypeHandler;
use Script\ValueHandler;

class MethodExpresion implements IExpresion{
  private $base;
  private $name;
  
  public function __construct(IExpresion $base, string $name){
    $this->base = $base;
    $this->name = $name;
  }
  
  public function getValue(ScriptContainer $container){
    $base = ReferenceHandler::getValue($this->base->getValue($container));
    $type = TypeHandler::type($base);
    if($type == "object" || $type == "string" || $type == "array"){
      return new ObjectMethodReference(ValueHandler::toObject($base, $container), $this->name, $this->base instanceof ThisExpresion, $container);
    }
    
    return new ObjectMethodReference($base, $this->name, $this->base instanceof SelfExpresion, $container);
  }
}