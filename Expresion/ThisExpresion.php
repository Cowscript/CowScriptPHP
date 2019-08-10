<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\Object\ObjectContainer;
use Script\Exception\ScriptRuntimeException;

class ThisExpresion implements IExpresion{
  public function getValue(ScriptContainer $container){
    if(!$container->hasOwner())
      throw new ScriptRuntimeException("Cant use the keyword 'this' outsite of method");
    
    $self = $container->getOwner();
    if(!($self instanceof ObjectContainer))
      throw new ScriptRuntimeException("Cant use the keyword 'this' when you are not in a object method");
    return $self;
  }
}