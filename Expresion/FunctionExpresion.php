<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\SCallable\CallableValue;
use Script\Complication\ComplicationType;

class FunctionExpresion implements IExpresion{
  private $data;
  
  function __construct(array $data){
    $this->data = $data;
  }
  
  public function getValue(ScriptContainer $container){
    $names = $this->data[2];
    $block = $this->data[4];
    $func = new CallableValue($this->data[0], function(ScriptContainer $c, array $arg) use($container, $names, $block){
      $container->pushStack();
      for($i=0;$i<count($names);$i++)
        $container->pushIdentify($names[$i], $arg[$i]);
      $result = $block->eval($container);
      $container->popStack();
      return $result->type == ComplicationType::RETURN ? $result->value : null;
    }, $this->data[3]);
    if($this->data[1] !== null)
      $func->setReturn($this->data[1]);
    return $func;
  }
}