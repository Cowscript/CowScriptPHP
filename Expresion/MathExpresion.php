<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\ValueHandler;
use Script\Reference\ReferenceHandler;

class MathExpresion implements IExpresion{
  private $first;
  private $sign;
  private $last;
  
  public function __construct(IExpresion $first, string $sign, IExpresion $last){
    $this->first = $first;
    $this->sign  = $sign;
    $this->last  = $last;
  }
  
  public function getValue(ScriptContainer $container){
    $first = ReferenceHandler::getValue($this->first->getValue($container));
    $last  = ReferenceHandler::getValue($this->last->getValue($container));
    return $this->sign == "+" ? $this->plus($first, $last, $container) : $this->minus($first, $last, $container);
  }
  
  private function plus($first, $last, ScriptContainer $container){
    //if one of them is a string do a string operation
    if(is_string($first) || is_string($last))
      return ValueHandler::toString($first, $container).ValueHandler::toString($last, $container);
    return ValueHandler::toInt($first, $container)+ValueHandler::toInt($last, $container);
  }
  
  private function minus($first, $last, ScriptContainer $container){
    return ValueHandler::toInt($first, $container) - ValueHandler::toInt($last, $container);
  }
}