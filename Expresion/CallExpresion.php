<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\Reference\ReferenceHandler;
use Script\Reference\ObjectMethodReference;
use Script\SCallable\CallableValue;
use Script\SCallable\CallableMethod;
use Script\TypeHandler;
use Script\Exception\ScriptRuntimeException;
use Script\ValueHandler;
use Script\Object\ClassContainer;

class CallExpresion implements IExpresion{
  private $func;
  private $arg;
  
  public function __construct(IExpresion $func, array $arg){
    $this->func = $func;
    $this->arg  = $arg;
  }
  
  public function getValue(ScriptContainer $container){
    $data = $this->func->getValue($container);
    $func = ReferenceHandler::getValue($data);
    
    if($func instanceof CallableMethod && $data instanceof ObjectMethodReference)
      return $func->methodCall($container, $data->getBase() instanceof ClassContainer ? $data->getBase() : ValueHandler::toObject($data->getBase(), $container), $this->prepareCall($func, $container));
    
    if($func instanceof CallableValue)
      return $func->call($container, $this->prepareCall($func, $container));
    
    throw new ScriptRuntimeException("Cant call ".TypeHandler::type($func)." as function");
  }
  
  private function prepareCall(CallableValue $func, ScriptContainer $container){
    $args = [];
    foreach($this->arg as $arg){
      $args[] = ReferenceHandler::getValue($arg->getValue($container));
    }
    return $func->resolveArguments($args, $container);
  }
}