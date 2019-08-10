<?php
namespace Script\Token;

class ScriptChar{
  public static function identifyStart(int $char) : bool{
    return $char >= 65 && $char <= 90
        || $char >= 97 && $char <= 122
        || $char == 95;
  }
  
  public static function identifyPart(int $char) : bool{
    return self::identifyStart($char) || self::numeric($char);
  }
  
  public static function numeric(int $char) : bool{
    return $char >= 48 && $char <= 57;
  }
}