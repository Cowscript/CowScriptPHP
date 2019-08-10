<?php
namespace Script\Expresion;

use Script\ScriptContainer;

class BoolExpresion implements IExpresion{
  private $value;
  
  public function __construct(string $value){
    $this->value = $value == "true";
  }
  
  public function getValue(ScriptContainer $container){
    return $this->value;
  }
}