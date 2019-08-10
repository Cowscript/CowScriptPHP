<?php
namespace Script\Plugin;

use Script\ScriptContainer;
use Script\Object\ClassContainer;
use Script\Object\ObjectContainer;
use Script\SCallable\CallableMethod;
use Script\Object\CallableArgs;

class RegexPlugin implements IPlugin{
  public function set(ScriptContainer $container) : bool{
    $class = new ClassContainer("Regex");
    $class->setPointer("str", "", true);
    
    $arg = new CallableArgs();
    $arg->pushArg("string");
    
    $class->setConstruct(new CallableMethod("", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      $obj->putPointer("str", $arg[0]);
    }, $arg));
    
    $match = new CallableMethod("match", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return preg_match("/".$obj->getPointer("str", $container)."/", $arg[0]) > 0;
    });
    $match->pushArg("string");
    $class->setMethod($match, true, false);
    
    $exec = new CallableMethod("exec", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      if(preg_match_all("/{$obj->getPointer("str", $container)}/", $arg[0], $p))
        return $p;
      return [];
    });
    $exec->pushArg("string");
    $class->setMethod($exec, true, false);
    $container->pushClass($class);
    return true;
  }
}