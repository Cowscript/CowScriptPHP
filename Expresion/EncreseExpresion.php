<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\Reference\ReferenceHandler;
use Script\ValueHandler;

class EncreseExpresion implements IExpresion{
  private $sign;
  private $expresion;
  
  public function __construct(IExpresion $expresion, string $sign){
    $this->expresion = $expresion;
    $this->sign      = $sign;
  }
  
  public function getValue(ScriptContainer $container){
    $ref = ReferenceHandler::toReference($this->expresion->getValue($container));
    $ref->put($this->sign == "++" ? ValueHandler::toInt($ref->getValue(), $container) + 1 : ValueHandler::toInt($ref->getValue(), $container) - 1);
  }
}