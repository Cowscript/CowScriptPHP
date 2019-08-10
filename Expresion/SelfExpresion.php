<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\Exception\ScriptRuntimeException;
use Script\Object\ClassContainer;

class SelfExpresion implements IExpresion{
  public function getValue(ScriptContainer $container){
    if(!$container->hasOwner())
      throw new ScriptRuntimeException("Can`t use the keyword 'self' outsite method");
    
    $owner = $container->getOwner();
    if(!($owner instanceof ClassContainer))
      throw new ScriptRuntimeException("Cant use the keyword 'self' outsite static method");
    return $owner;
  }
}