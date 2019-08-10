<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\ValueHandler;
use Script\Reference\ReferenceHandler;

class BoolBindExpresion implements IExpresion{
  private $first;
  private $sign;
  private $last;
  
  public function __construct(IExpresion $first, string $sign, IExpresion $last){
    $this->first = $first;
    $this->sign  = $sign;
    $this->last  = $last;
  }
  
  public function getValue(ScriptContainer $container){
    if($this->sign == "||"){
      if(ValueHandler::toBool(ReferenceHandler::getValue($this->first->getValue($container))))
         return true;
      return ValueHandler::toBool(ReferenceHandler::getValue($this->first->getValue($container)));
    }
    if(!ValueHandler::toBool(ReferenceHandler::getValue($this->first->getValue($container))))
      return false;
    return ValueHandler::toBool(ReferenceHandler::getValue($this->last->getValue($container)));
  }
}