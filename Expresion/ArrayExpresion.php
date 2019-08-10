<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\ValueHandler;
use Script\Reference\ReferenceHandler;

class ArrayExpresion implements IExpresion{
  private $data;
  
  public function __construct(array $data){
    $this->data = $data;
  }
  
  public function getValue(ScriptContainer $container){
    $result = [];
    foreach($this->data as $value){
      $val = ReferenceHandler::getValue($value[1]->getValue($container));
      if($value[0] == null)
        $result[] = $val;
      else
        $result[ValueHandler::toInt(ReferenceHandler::getValue($value[0]->getValue($container)), $container)] = $val;
    }
    return $result;
  }
}