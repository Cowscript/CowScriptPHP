<?php
namespace Script\Plugin;

use Script\ScriptContainer;
use Script\Object\CallableArgs;
use Script\SCallable\CallableValue;

class MathPlugin implements IPlugin{
  public function set(ScriptContainer $container) : bool{
    $args = new CallableArgs();
    $args->pushArg("int");
    $container->pushFunction(new CallableValue("sin", function(ScriptContainer $container, array $arg){
      return sin($arg[0]);
    }, $args));
    $container->pushFunction(new CallableValue("cos", function(ScriptContainer $container, array $arg){
      return cos($arg[0]);
    }, $args));
    $container->pushFunction(new CallableValue("tan", function(ScriptContainer $container, array $arg){
      return tan($arg[0]);
    }, $arg));
    return true;
  }
}