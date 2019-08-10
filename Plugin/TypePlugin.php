<?php
namespace Script\Plugin;

use Script\ScriptContainer;
use Script\SCallable\CallableValue;
use Script\Object\ObjectValue;
use Script\TypeHandler;

class TypePlugin implements IPlugin{
  public function set(ScriptContainer $container) : bool{
    $callable = new CallableValue("type", function($controle, array $arg){
      if($arg[0] instanceof ObjectValue && $arg[0]->getName() == $arg[1])
        return true;
      return TypeHandler::type($arg[0]) == $arg[1];
    });
    $callable->pushArg(null);//null means any type
    $callable->pushArg("string");
    $container->pushFunction($callable);
    
    $callable = new CallableValue("toInt", function(string $context){
      return intval($context);
    });
    $callable->pushArg("string");
    $container->pushFunction($callable);
    
    return true;
  }
}