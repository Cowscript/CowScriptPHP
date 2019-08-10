<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\ValueHandler;
use Script\Reference\ReferenceHandler;

class AskExpresion implements IExpresion{
  private $test;
  private $onTrue;
  private $onFalse;
  
  public function __construct(IExpresion $test, IExpresion $true, IExpresion $false){
    $this->test    = $test;
    $this->onTrue  = $true;
    $this->onFalse = $false;
  }
  
  public function getValue(ScriptContainer $container){
    if(ValueHandler::toBool(ReferenceHandler::getValue($this->test->getValue($container))))
      return $this->onTrue->getValue($container);
    return $this->onFalse->getValue($container);
  }
}