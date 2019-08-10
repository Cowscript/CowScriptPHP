<?php
namespace Script\SCallable;

use Script\Object\CallableArgs;
use Script\Token\Tokenizer;
use Script\Token\TokenType;
use Script\Expresion\ExpresionEvulvor;

class CallBuilder{
  protected static function getFuncArg(CallableArgs $value, Tokenizer $token){
    if($token->buffer()->type != TokenType::IDENTIFY && ($token->buffer()->type != TokenType::KEYWORD || $token->buffer()->context != "function"))
      throw new ScriptParseException("Expected identify as function args or typed arg", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    $t = $token->buffer()->context;
    if($token->next()->type == TokenType::IDENTIFY){
      $type = $t;
      $t = $token->buffer()->context;
      if($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == "="){
        $token->next();
        $value->pushArg($type, ExpresionEvulvor::expresion($token));
      }else
        $value->pushArg($type);
      $token->next();
    }else{
      if($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == "="){
        $token->next();
        $value->pushArg(null, ExpresionEvulvor::expresion($token));
      }else
        $value->pushArg(null); 
    }
    return $t;
  }
}