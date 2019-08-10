<?php
namespace Script\Item;

use Script\ScriptContainer;
use Script\Object\ClassContainer;
use Script\Object\ObjectContainer;
use Script\SCallable\CallableMethod;
use Script\Object\CallableArgs;
use Script\ValueHandler;

class ScriptString{
  public static function build(ScriptContainer $container){
    $class = new ClassContainer("String");
    $arg = new CallableArgs();
    $arg->pushArg("string");
    $class->setConstruct(new CallableMethod("", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      $obj->putPointer("str", $arg[0]);
    }, $arg));
    
    $length = new CallableMethod("length", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      return strlen($obj->getPointer("str", $container));
    });
    $class->setMethod($length, true);
    
    $substr = new CallableMethod("substr", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      if($arg[1] == -1)
        $result = substr($obj->getPointer("str", $container), $arg[0]);
      else
        $result = substr($obj->getPointer("str", $container), $arg[0], $arg[1]);
      return $result === false ? -1 : $result;
    });
    $substr->pushArg("int");
    $substr->pushArg("int", -1);
    $class->setMethod($substr, true);
    
    $indexof = new CallableMethod("indexOf", function(ScriptContainer $container, ObjectContainer $obj, array $arg){
      $v = strpos($obj->getPointer("str", $container), $arg[0]);
      return $v === false ? -1 : $v;
    });
    $indexof->pushArg("string");
    $class->setMethod($indexof, true);
    
    $tochars = new CallableMethod("toChars", function(ScriptContainer $container, ObjectContainer $obj, array $args){
      $return = [];
      $str = $obj->getPointer("str", $container);
      for($i=0;$i<strlen($str);$i++){
        $return[] = $str[$i];
      }
      return $return;
    });
    $class->setMethod($tochars, true);
    
    //------------ V0.2
    $class->setMethod(new CallableMethod("toString", function(ScriptContainer $container, ObjectContainer $obj, array $args){
      return $obj->getPointer("str", $container);
    }), true);
    
    $class->setMethod(new CallableMethod("toLower", function(ScriptContainer $container, ObjectContainer $obj, array $args){
      return strtolower($obj->getPointer("str", $container));
    }), true);
    
    $class->setMethod(new CallableMethod("toUpper", function(ScriptContainer $container, ObjectContainer $obj, array $args){
      return strtoupper($obj->getPointer("str", $container));
    }), true);
    
    //----------- V0.3
    $arg = new CallableArgs();
    $arg->pushArg("string");
    $arg->pushArg("array", []);
    $class->setStaticMethod(new CallableMethod("format", function(ScriptContainer $c, ClassContainer $class, array $args) use($container){
      return vsprintf($args[0], array_map(function($n) use($container){
        return ValueHandler::toString($n, $container);
      }, $args[1]));
    }, $arg), true);
    
    $container->pushClass($class);
  }
}