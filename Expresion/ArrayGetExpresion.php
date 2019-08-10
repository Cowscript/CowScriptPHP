<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\Reference\ReferenceHandler;
use Script\Reference\ArrayReference;
use Script\Exception\ScriptRuntimeException;
use Script\ValueHandler;

class ArrayGetExpresion implements IExpresion{
  private $base;
  private $arg;
  
  public function __construct(IExpresion $base, ?IExpresion $arg){
    $this->base = $base;
    $this->arg  = $arg;
  }
  
  public function getValue(ScriptContainer $container){
    $value = ReferenceHandler::getValue($this->base->getValue($container));
    if(!is_array($value))
      throw new ScriptRuntimeException("Cant get array data out of non array value");
    return new ArrayReference($value, $this->arg == null ? null : ValueHandler::toInt($this->arg->getValue($container)));
  }
}