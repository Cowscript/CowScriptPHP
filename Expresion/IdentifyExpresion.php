<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\Exception\ScriptRuntimeException;
use Script\Reference\IdentifyReference;

class IdentifyExpresion implements IExpresion{
  private $name;
  
  public function __construct(string $name){
    $this->name = $name;
  }
  
  public function getName() : string{
    return $this->name;
  }
  
  public function getValue(ScriptContainer $container){
    return new IdentifyReference($this->name, $container);
  }
}