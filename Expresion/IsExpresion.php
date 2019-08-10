<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\ValueHandler;
use Script\Reference\ReferenceHandler;

class IsExpresion implements IExpresion{
  private $obj;
  private $name;
  
  public function __construct(IExpresion $obj, string $name){
    $this->obj = $obj;
    $this->name = $name;
  }
  
  public function getValue(ScriptContainer $container){
    return ValueHandler::toObject(ReferenceHandler::getValue($this->obj->getValue($container)), $container)->is($this->name);
  }
}