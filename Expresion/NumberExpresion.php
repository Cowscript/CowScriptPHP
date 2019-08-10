<?php
namespace Script\Expresion;

use Script\ScriptContainer;

class NumberExpresion implements IExpresion{
  private $num;
  
  public function __construct(float $number){
    $this->num = $number;
  }
  
  public function getValue(ScriptContainer $container){
    return $this->num;
  }
}