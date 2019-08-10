<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\SCallable\ClassBuilder;

class ClassExpresion implements IExpresion{
  private $data;
  
  function __construct(array $data){
    $this->data = $data;
  }
  
  public function getValue(ScriptContainer $container){
    return ClassBuilder::buildClass($this->data, $container);
  }
}