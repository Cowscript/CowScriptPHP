<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\ValueHandler;
use Script\Reference\ReferenceHandler;

class EquelExpresion implements IExpresion{
  private $first;
  private $sign;
  private $last;
  
  public function __construct(IExpresion $first, string $sign, IExpresion $last){
    $this->first = $first;
    $this->sign = $sign;
    $this->last  = $last;
  }
  
  public function getValue(ScriptContainer $container){
    $value =  ValueHandler::isEquel(
      ReferenceHandler::getValue($this->first->getValue($container)),
      ReferenceHandler::getValue($this->last->getValue($container))
      );
    return $this->sign == "==" ? $value : !$value;
  }
}