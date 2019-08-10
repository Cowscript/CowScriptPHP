<?php
namespace Script\Item;

use Script\ScriptContainer;
use Script\Object\ClassContainer;
use Script\Object\ObjectContainer;
use Script\SCallable\CallableMethod;
use Script\Object\CallableArgs;
use Script\ValueHandler;

class ScriptArray{
  public static function build(ScriptContainer $container){
    $class = new ClassContainer("Array");
    $class->setPointer("array", null, true);
    $arg = new CallableArgs();
    $arg->pushArg("array");
    $class->setConstruct(new CallableMethod("", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      $obj->putPointer("array", $arg[0]);
    }, $arg));
    
    $class->setMethod(new CallableMethod("length", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return count($obj->getPointer("array", $container));
    }), true);
    
    $arg = new CallableArgs();
    $arg->pushArg("int");
    $class->setMethod(new CallableMethod("hasKey", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return array_key_exists(ValueHandler::toInt($arg[0], $container), $obj->getPointer("array", $container));
    }, $arg), true);
    
    $arg = new CallableArgs();
    $arg->pushArg(null);
    $class->setMethod(new CallableMethod("hasValue", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return in_array($arg[0], $obj->getPointer("array", $container));
    }, $arg), true);
    
    $container->pushClass($class);
  }
}