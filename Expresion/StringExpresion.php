<?php
namespace Script\Expresion;

use Script\ScriptContainer;

class StringExpresion implements IExpresion{
  private $str;
  
  public function __construct(string $str){
    $this->str = $str;
  }
  
  public function getValue(ScriptContainer $container){
    return $this->str;
  }
}