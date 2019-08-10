<?php
namespace Script;

use Script\SCallable\CallableValue;
use Script\Object\ClassContainer;
use Script\Object\ObjectContainer;
use Script\Exception\ScriptRuntimeException;

class TypeHandler{
  public static function convert(string $type, $value, ScriptContainer $container){
    switch($type){
      case "null":
        return null;
      case "string":
        return ValueHandler::toString($value, $container);
      case "int":
        return ValueHandler::toInt($value, $container);
      case "bool":
        return ValueHandler::toBool($value);
      case "array":
        return ValueHandler::toArray($value);
      case "function":
        return ValueHandler::toFunction($value);
      case "class":
        return ValueHandler::toArray($value);
      case "object":
        return ValueHandler::toObject($value, $container);
      default:
        $obj = ValueHandler::toObject($value, $container);
        if($obj->getName() == $type)
          return $obj;
        throw new ScriptRuntimeException("Cant convert ".self::type($value)." {$type}");
    }
    exit($type);
  }
  
  public static function type($value) : string{
    if($value === null)
      return "null";
    if(is_string($value))
      return "string";
    if(is_int($value) || is_float($value))
      return "int";
    if(is_bool($value))
      return "bool";
    if(is_array($value))
      return "array";
    if($value instanceof CallableValue)
      return "function";
    if($value instanceof ClassContainer)
      return "class";
    if($value instanceof ObjectContainer)
      return "object";
    
    throw new ScriptRuntimeException("Cant detect type for: ".gettype($value));
  }
}