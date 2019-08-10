<?php
namespace Script\Item;

use Script\ScriptContainer;
use Script\Object\ClassContainer;
use Script\Object\CallableArgs;
use Script\SCallable\CallableMethod;
use Script\Object\ObjectContainer;

class ScriptInt{
  public static function build(ScriptContainer $container){
    $class = new ClassContainer("Int");
    $class->setPointer("number", -1, true);
    $arg = new CallableArgs();
    $arg->pushArg("int");
    $class->setConstruct(new CallableMethod("", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      $obj->putPointer("number", $arg[0]);
    }, $arg));
    
    $class->setMethod(new CallableMethod("toInt", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return $obj->getPointer("number", $container);
    }), true);
    
    $container->pushClass($class);
  }
}