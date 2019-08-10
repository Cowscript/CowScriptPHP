<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\Reference\ReferenceHandler;
use Script\ValueHandler;

class NotExpresion implements IExpresion{
  private $exp;
  
  public function __construct(IExpresion $exp){
    $this->exp = $exp;
  }
  
  public function getValue(ScriptContainer $container){
    return !ValueHandler::toBool(ReferenceHandler::getValue($this->exp->getValue($container)));
  }
}