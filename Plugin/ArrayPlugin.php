<?php
namespace Script\Plugin;

use Script\ScriptContainer;
use Script\SCallable\CallableValue;

class ArrayPlugin implements IPlugin{
  public function set(ScriptContainer $container) : bool{
    $callable = new CallableValue("count", function(ScriptContainer $container, array $array){
      return count($array[0]);
    });
    $callable->pushArg("array");
    $container->pushFunction($callable);
    
    $callable = new CallableValue("hasValue", function(array $array, $context){
      return in_array($context, $array);
    });
    
    $callable->pushArg("array");
    $callable->pushArg(null);
    $container->pushFunction($callable);
    
    $callable = new CallableValue("hasKey", function(array $array, $context){
      return in_array($context, array_keys($array));
    });
    $callable->pushArg("array");
    $callable->pushArg(null);
    $container->pushFunction($callable);
    
    return true;
  }
}