<?php
namespace Script\SCallable;

use Script\Token\Tokenizer;
use Script\Token\TokenType;
use Script\Object\CallableArgs;
use Script\BlockBuilder;
use Script\Exception\ScriptParseException;

class FunctionBuilder extends CallBuilder{
  public static function parseFunction(Tokenizer $token, bool $name){
    if($name){
      if($token->next()->type != TokenType::IDENTIFY)
        throw new ScriptParseException("Missing function name after the function keyword", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
      $name = $token->buffer()->context;
      $type = null;
      if($token->next()->type == TokenType::IDENTIFY){
        $type = $name;
        $name = $token->buffer()->context;
        $token->next();
      }
    }else{
      $name = "<inline function>";
      $type = null;
      if($token->buffer()->type == TokenType::IDENTIFY){
        $type = $token->buffer()->context;
        $token->next();
      }
    }
    
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != "(")
      throw new ScriptParseException("Missing ( before function arguments list got ".$token->buffer()->context, $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    $arg = new CallableArgs();
    $names = [];
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != ")"){
      $names[] = self::getFuncArg($arg, $token);
      while($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == ","){
        $token->next();
        $names[] = self::getFuncArg($arg, $token); 
      }
    }
    
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ")"){
      throw new ScriptParseException("Missing ) in end of function args list", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    }
    
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != "{")
      throw new ScriptParseException("Missing { in start of function body", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    return [$name, $type, $names, $arg, BlockBuilder::getBlock($token)];
  }
}