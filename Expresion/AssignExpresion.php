<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\Reference\ReferenceHandler;

class AssignExpresion implements IExpresion{
  private $first;
  private $second;
  
  public function __construct(IExpresion $first, IExpresion $second){
    $this->first = $first;
    $this->second = $second;
  }
  
  public function getValue(ScriptContainer $container){
    return ReferenceHandler::toReference($this->first->getValue($container))->put(ReferenceHandler::getValue($this->second->getValue($container)));
  }
}