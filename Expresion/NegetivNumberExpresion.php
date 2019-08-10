<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\ValueHandler;
use Script\Reference\ReferenceHandler;

class NegetivNumberExpresion implements IExpresion{
  private $number;
  
  public function __construct(IExpresion $number){
    $this->number = $number;
  }
  
  public function getValue(ScriptContainer $container){
    return -ValueHandler::toInt(ReferenceHandler::getValue($this->number->getValue($container)), $container);
  }
}