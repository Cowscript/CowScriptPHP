<?php
namespace Script;

use Script\Exception\ScriptRuntimeException;
use Script\Object\ObjectContainer;
use Script\Object\ClassContainer;
use Script\SCallable\CallableValue;

class ValueHandler{
  public static function toFunction($value){
    if($value instanceof CallableValue)
      return $value;
    
    throw new ScriptRuntimeException("Cant convert ".TypeHandler::type($value)." to function");
  }
  
  public static function toObject($obj, ScriptContainer $container) : ObjectContainer{
    if($obj instanceof ObjectContainer)
      return $obj;
    
    if(is_string($obj)){
      $str = $container->getIdentify("String");
      $o = new ObjectContainer($str);
      $c = $str->getConstruct();
      $c->methodCall($container, $o, $c->resolveArguments([$obj], $container));
      return $o;
    }
    
    if(is_array($obj)){
      $array = $container->getIdentify("Array");
      $o = new ObjectContainer($array);
      $c = $array->getConstruct();
      $c->methodCall($container, $o, $c->resolveArguments([$obj], $container));
      return $o;
    }
    
    throw new ScriptRuntimeException("Cant convert '".TypeHandler::type($obj)."' to object");
  }
  
  public static function toClass($obj) : ClassContainer{
    if($obj instanceof ClassContainer)
      return $obj;
    throw new ScriptRuntimeException("Cant convert '".TypeHandler::type($obj)."' to class");
  }
  
  public static function toInt($value, ScriptContainer $container) : int{
    if(is_float($value) || is_int($value))
      return (int)$value;
    
    if(TypeHandler::type($value) == "object" && $value->hasMethod("toInt") && $value->methodPublic("toInt")){
      $method = $value->getMethod("toInt");
      if($method->getArg()->count() == 0){
        return self::toInt($method->methodCall($container, $value, []), $container);
      }
    }
    
    throw new ScriptRuntimeException("Cant convert '".TypeHandler::type($value)."' to int");
  }
  
  public static function toString($value, ScriptContainer $container) : string{
    if($value === null)
      return "null";
    
    if(is_string($value))
      return $value;
    
    if(is_float($value) || is_int($value)){
      if($value == 0)
        return "0";
      return (string)$value;
    }
    
    if(TypeHandler::type($value) == "class" && $value->hasStaticMethod("toString") && $value->staticMethodPublic("toString")){
      $method = $value->getStaticMethod("toString");
      if($method->getArg()->count() == 0){
        return self::toString($method->methodCall($container, $value, []), $container);
      }
    }
    
    if(TypeHandler::type($value) == "object" && $value->hasMethod("toString") && $value->methodPublic("toString")){
      $method = $value->getMethod("toString");
      if($method->getArg()->count() == 0){
        return self::toString($method->methodCall($container, $value, []), $container);
      }
    }
    
    throw new ScriptRuntimeException("Cant convert '".TypeHandler::type($value)."' to string");
  }
  
  public static function toBool($value) : bool{
    if(is_bool($value))
      return $value;
    throw new ScriptRuntimeException("Cant convert '".TypeHandler::type($value)."' to bool");
  }
  
  public static function toArray($value){
    if(is_array($value))
      return $value;
    throw new ScriptRuntimeException("Cant convert '".TypeHandler::type($value)."' to array");
  }
  
  public static function isEquel($first, $second) : bool{
    //if type is not equel stop here
    if(TypeHandler::type($first) != TypeHandler::type($second))
      return false;
    return $first == $second;
  }
}