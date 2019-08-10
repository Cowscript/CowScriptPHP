<?php
namespace Script\Reference;

use Script\Exception\ScriptRuntimeException;
use Script\TypeHandler;

class ReferenceHandler{
  public static function toReference($value){
    if(!($value instanceof IReference))
      throw new ScriptRuntimeException("Cant convert ".TypeHandler::type($value)." is not a reference");
    return $value;
  }
  
  public static function getValue($value){
    if($value instanceof IReference)
      return $value->getValue();
    return $value;
  }
}