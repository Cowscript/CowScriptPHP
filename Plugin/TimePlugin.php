<?php
namespace Script\Plugin;

use Script\ScriptContainer;
use Script\Object\ClassContainer;
use Script\Object\ObjectContainer;
use Script\SCallable\CallableMethod;
use Script\Object\CallableArgs;

class TimePlugin implements IPlugin{
  public function set(ScriptContainer $container) : bool{
    $class = new ClassContainer("Time");
    $class->setPointer("time", "", true);
    $arg = new CallableArgs();
    $arg->pushArg("int", -1);
    $arg->pushArg("int", -1);
    $arg->pushArg("int", -1);
    $arg->pushArg("int", -1);
    $arg->pushArg("int", -1);
    $arg->pushArg("int", -1);
    $class->setConstruct(new CallableMethod("", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      $r = [
        $arg[3] == -1 ? date("H") : $arg[3],
        $arg[4] == -1 ? date("i") : $arg[4],
        $arg[5] == -1 ? date("s") : $arg[5],
        $arg[1] == -1 ? date("n") : $arg[1],
        $arg[2] == -1 ? date("j") : $arg[2],
        $arg[0] == -1 ? date("Y") : $arg[0]
        ];
      $obj->putPointer("time", call_user_func_array("mktime", $r));
    }, $arg));
    
    $class->setMethod(new CallableMethod("year", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return (int)date("Y", $obj->getPointer("time", $container));
    }), true);
    
    $class->setMethod(new CallableMethod("month", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return (int)date("n", $obj->getPointer("time", $container));
    }), true);
    
    $class->setMethod(new CallableMethod("week", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return (int)date("W", $obj->getPointer("time", $container));
    }), true);
    
    $class->setMethod(new CallableMethod("dayOfWeek", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return (int)date("w", $obj->getPointer("time", $container));
    }), true);
    
    $class->setMethod(new CallableMethod("day", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return (int)date("j", $obj->getPointer("time", $container));
    }), true);
    
    $class->setMethod(new CallableMethod("hour", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return (int)date("H", $obj->getPointer("time", $container));
    }), true);
    
    $class->setMethod(new CallableMethod("minute", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return (int)date("i", $obj->getPointer("time", $container));
    }), true);
    
    $class->setMethod(new CallableMethod("millisecond", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return (int)date("s", $obj->getPointer("time", $container));
    }), true);
    
    $container->pushClass($class);
    return true;
  }
}