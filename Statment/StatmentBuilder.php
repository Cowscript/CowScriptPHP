<?php
namespace Script\Statment;

use Script\Token\Tokenizer;
use Script\Token\TokenType;
use Script\Expresion\ExpresionEvulvor;
use Script\Expresion\IExpresion;
use Script\BlockBuilder;
use Script\Object\CallableArgs;
use Script\SCallable\FunctionBuilder;
use Script\SCallable\ClassBuilder;

class StatmentBuilder{
  public static function getNextStatment(Tokenizer $token) : IStatment{
    if($token->buffer()->type == TokenType::KEYWORD){
        switch($token->buffer()->context){
          case "use":
            return self::getUse($token);
          case "function":
          case "func":
            return new FunctionStatment(FunctionBuilder::parseFunction($token, true));
          case "if":
            return self::getIf($token);
          case "return":
            return self::getReturn($token);
          case "class":
            return new ClassStatment(ClassBuilder::parseClass($token, true));
          case "foreach":
            return self::getForeach($token);
          case "for":
            return self::getFor($token);
          case "while":
            return self::getWhile($token);
          case "continue":
            return self::getContinue($token);
          case "break":
            return self::getBreak($token);
        }
      }
      return ExpresionEvulvor::evulvoe($token);
  }
  
  private static function getForeach(Tokenizer $token) : ForeachStatment{
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != "(")
      throw new ScriptParseException("Missing ( after the keyword foreach", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    $token->next();
    $exp = ExpresionEvulvor::expresion($token);
    
    if($token->buffer()->type != TokenType::KEYWORD || $token->buffer()->context != "as")
      throw new ScriptParseException("Missing the keyword 'as' after foreach expresion", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    if($token->next()->type != TokenType::IDENTIFY)
      throw new ScriptParseException("Missing identify after the keyword 'as'", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    $identify = $token->buffer()->context;
    
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != ")")
      throw new ScriptParseException("Missing ) after identify", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    $token->next();
    return new ForeachStatment($exp, $identify, BlockBuilder::getBlock($token));
  }
  
  private static function getBreak(Tokenizer $token) : BreakStatment{
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != ";")
      throw new ScriptRuntimeException("After the break keyword there must be a ;", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    return new BreakStatment();
  }
  
  private static function getContinue(Tokenizer $token) : ContinueStatment{
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != ";")
      throw new ScriptParseException("Aftet the continue keyword there must be a ;", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    return new ContinueStatment();
  }
  
  private static function getWhile(Tokenizer $token) : WhileStatment{
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != "(")
      throw new ScriptParseException("Missing ( after the keyword while", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    
    $expresion = ExpresionEvulvor::expresion($token);
    
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ")")
      throw new ScriptParseException("Missing ) in end of while expresion", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    
    return new WhileStatment($expresion, BlockBuilder::getBlock($token));
  }
  
  private static function getReturn(Tokenizer $token) : ReturnStatment{
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != ";")
      $return = ExpresionEvulvor::expresion($token);
    else
      $return = new NullExpresion();
    
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ";")
      throw new ScriptParseException("Missing ; after return statment", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    return new ReturnStatment($return);
  }
  
  private static function getFor(Tokenizer $token) : ForStatment{
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != "(")
      throw new ScriptParseException("Missing ( after the keyword '('", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    
    $first = ExpresionEvulvor::assign($token);
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ";")
      throw new ScriptParseException("Missing ; after the first expresion", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    
    $second = ExpresionEvulvor::assign($token);
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ";")
      throw new ScriptParseException("Missing ; after the second expresion", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    
    $last = ExpresionEvulvor::assign($token);
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ")")
      throw new ScriptParseException("Missing ) after the last expresion", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    
    return new ForStatment($first, $second, $last, BlockBuilder::getBlock($token));
  }
  
  private static function getIf(Tokenizer $token) : IfStatment{
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != "(")
      throw new ScriptParseException("Missing '('", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    $token->next();
    $expresion = ExpresionEvulvor::expresion($token);
    
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ")")
      throw new ScriptParseException("Missing )", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    $block = BlockBuilder::getBlock($token);
    if($token->buffer()->type == TokenType::KEYWORD){
      if($token->buffer()->context == "elseif"){
        return new IfStatment($expresion, $block, self::getIf($token));
      }elseif($token->buffer()->context == "else"){
        $token->next();
        return new IfStatment($expresion, $block, new ElseStatment(BlockBuilder::getBlock($token)));
      }
    }
    return new IfStatment($expresion, $block, null);
  }
  
  private static function getUse(Tokenizer $token) : UseStatment{
    $u = [self::getUsePart($token)];
    while($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == ","){
      $u[] = self::getUsePart($token);
    }
    
    if($token->buffer()->type != TokenType::PUNCTOR && $token->buffer()->context != ";")
      throw new ScriptParseException("Missing ; after use statment");
    $token->next();
    return new UseStatment($u);
  }
  
  private static function getUsePart(Tokenizer $token) : IExpresion{
    $token->next();
    return ExpresionEvulvor::expresion($token);
  }
}