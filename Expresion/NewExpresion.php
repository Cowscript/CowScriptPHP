<?php
namespace Script\Expresion;

use Script\ScriptContainer;
use Script\TypeHandler;
use Script\ValueHandler;
use Script\Object\ClassContainer;
use Script\Object\ObjectContainer;
use Script\Reference\ReferenceHandler;
use Script\SCallable\CallableMethod;
use Script\Exception\ScriptRuntimeException;

class NewExpresion implements IExpresion{
  private $name;
  private $arg;
  
  public function __construct(string $name, array $arg){
    $this->name = $name;
    $this->arg  = $arg;
  }
  
  public function getValue(ScriptContainer $container){
    $identify = $container->getIdentify($this->name);
    $object = new ObjectContainer(ValueHandler::toClass($identify));
    
    if($identify->hasConstruct()){
      $c = $identify->getConstruct();
      $c->methodCall($container, $object, $this->getArgs($c, $container));
    }
    return $object;
  }
  
  private function getArgs(CallableMethod $construct, ScriptContainer $container) : array{
    $return = [];
    foreach($this->arg as $arg){
      $return[] = ReferenceHandler::getValue($arg->getValue($container));
    }
    return $construct->resolveArguments($return, $container);
  }
}