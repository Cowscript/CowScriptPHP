<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\ValueHandler;
use Script\Reference\ReferenceHandler;

class CompareExpresion implements IExpresion{
  private $first;
  private $sign;
  private $last;
  
  public function __construct(IExpresion $first, string $sign, IExpresion $last){
    $this->first = $first;
    $this->sign  = $sign;
    $this->last  = $last;
  }
  
  public function getValue(ScriptContainer $container){
    $first = ValueHandler::toInt(ReferenceHandler::getValue($this->first->getValue($container)), $container);
    $last  = ValueHandler::toInt(ReferenceHandler::getValue($this->last->getValue($container)), $container);
    switch($this->sign){
      case ">":
        return $first > $last;
      case ">=":
        return $first >= $last;
      case "<":
        return $first < $last;
      case "<=":
        return $first <= $last;
    }
  }
}