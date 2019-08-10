<?php
namespace Script\Plugin;

use Script\ScriptContainer;
use Script\SCallable\CallableValue;
use Script\Exception\ScriptRuntimeException;

class ErrorPlugin implements IPlugin{
  public function set(ScriptContainer $container) : bool{
    $errorCallback = null;
    $callable = new CallableValue("error", function(ScriptContainer $container, array $arg) use(&$errorCallback){
      if($errorCallback == null)
        throw new ScriptRuntimeException($arg[0]);
      $errorCallback->call($container, [$arg[0]]);
    });
    $callable->pushArg("string");
    $container->pushFunction($callable);
    
    $callable = new CallableValue("errorCallback", function(ScriptContainer $container, array $arg) use(&$errorCallback){
      $errorCallback = $arg[0];
    });
    $callable->pushArg("function");
    $container->pushFunction($callable);
    return true;
  }
}