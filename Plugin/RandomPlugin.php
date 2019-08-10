<?php
namespace Script\Plugin;

use Script\ScriptContainer;
use Script\Object\ClassContainer;
use Script\SCallable\CallableMethod;
use Script\Object\CallableArgs;

class RandomPlugin implements IPlugin{
  public function set(ScriptContainer $container) : bool{
    $class = new ClassContainer("Random");
    
    $arg = new CallableArgs();
    $arg->pushArg("int");
    $class->setMethod(new CallableMethod("seed", function(ScriptContainer $container, $obj, array $arg){
      mt_srand($arg[0]);
    }, $arg), true);
    
    $arg = new CallableArgs();
    $arg->pushArg("int");
    $arg->pushArg("int");
    $class->setMethod(new CallableMethod("next", function(ScriptContainer $container, $obj, array $arg){
      return mt_rand($arg[0], $arg[1]);
    }, $arg), true);
    
    $container->pushClass($class);
    return true;
  }
}