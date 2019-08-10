<?php
namespace Script\Expresion;

use Script\Token\Tokenizer;
use Script\Token\TokenType;
use Script\Statment\IStatment;
use Script\Statment\ExpresionStatment;
use Script\Exception\ScriptParseException;
use Script\SCallable\FunctionBuilder;
use Script\SCallable\ClassBuilder;

class ExpresionEvulvor{
  public static function evulvoe(Tokenizer $token) : IStatment{
    $expresion = self::assign($token);
    if($token->buffer()->type != TokenType::PUNCTOR && $token->buffer()->context != ";")
      throw new ScriptParseException("Missing ; after expresion statment", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    return new ExpresionStatment($expresion);
  }
  
  public static function assign(Tokenizer $token) : IExpresion{
    $exp = self::expresion($token);
    if($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == "="){
      $token->next();
      return new AssignExpresion($exp, self::expresion($token));
    }
    return $exp;
  }
  
  public static function expresion(Tokenizer $token) : IExpresion{
    $expresion = self::boolBind($token);
    if($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == "?"){
      $token->next();
      $true = self::expresion($token);
      if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ":")
        throw new ScriptParseException("Missing : after the true expresion in ask expresion", $token->buffer()->getURI(), $token->buffer()->getStartLine());
      $token->next();
      return new AskExpresion($expresion, $true, self::expresion($token));
    }
    return $expresion;
  }
  
  private static function boolBind(Tokenizer $token) : IExpresion{
    $exp = self::equel($token);
    if($token->buffer()->type == TokenType::PUNCTOR && ($token->buffer()->context == "&&" || $token->buffer()->context == "||")){
      $sign = $token->buffer()->context;
      $token->next();
      return new BoolBindExpresion($exp, $sign, self::boolBind($token));
    }
    return $exp;
  }
  
  private static function equel(Tokenizer $token) : IExpresion{
    $exp = self::compare($token);
    if($token->buffer()->type == TokenType::PUNCTOR && ($token->buffer()->context == "==" || $token->buffer()->context == "!=")){
      $sign = $token->buffer()->context;
      $token->next();
      return new EquelExpresion($exp, $sign, self::equel($token));
    }
    return $exp;
  }
  
  private static function compare(Tokenizer $token) : IExpresion{
    $exp = self::math($token);
    if($token->buffer()->type == TokenType::PUNCTOR && ($token->buffer()->context == ">" || $token->buffer()->context == ">=" || $token->buffer()->context == "<" || $token->buffer()->context == "<=")){
      $sign = $token->buffer()->context;
      $token->next();
      return new CompareExpresion($exp, $sign, self::compare($token));
    }
    return $exp;
  }
  
  private static function math(Tokenizer $token) : IExpresion{
    $exp = self::additiv($token);
    if($token->buffer()->type == TokenType::PUNCTOR && ($token->buffer()->context == "-" || $token->buffer()->context == "+")){
      $sign = $token->buffer()->context;
      $token->next();
      return new MathExpresion($exp, $sign, self::math($token));
    }
    return $exp;
  }
  
  private static function additiv(Tokenizer $token) : IExpresion{
    $exp = self::after($token);
    if($token->buffer()->type == TokenType::PUNCTOR && ($token->buffer()->context == "*" || $token->buffer()->context == "/" || $token->buffer()->context == "^")){
      $sign = $token->buffer()->context;
      $token->next();
      return new AdditivExpresion($exp, $sign, self::additiv($token));
    }
    return $exp;
  }
  
  private static function after(Tokenizer $token) : IExpresion{
    $expresion = self::prefix($token);
    if($token->buffer()->type == TokenType::PUNCTOR && ($token->buffer()->context == "++" || $token->buffer()->context == "--")){
      $sign = $token->buffer()->context;
      $token->next();
      return new EncreseExpresion($expresion, $sign);
    }
    return $expresion;
  }
  
  private static function prefix(Tokenizer $token) : IExpresion{
    if($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == "-"){
      $token->next();
      return new NegetivNumberExpresion(self::is($token));
    }
    
    if($token->buffer()->type == TokenType::KEYWORD && $token->buffer()->context == "new"){
      if($token->next()->type != TokenType::IDENTIFY)
        throw new ScriptParseException("After new keyword there must be a identify", $token->buffer()->getURI(), $token->buffer()->getStartLine());
      $name = $token->buffer()->context;
     
      if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != "(")
        throw new ScriptParseException("Missing '(' after class name in new expresion", $token->buffer()->getURI(), $token->buffer()->getStartLine());
      
      $arg = [];
      if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != ")"){
        $arg[] = self::assign($token);
        while($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == ","){
          $token->next();
          $arg[] = self::assign($token);
        }
      }
    
      if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ")")
        throw new ScriptParseException("Missing ) ind end of new expresion", $token->buffer()->getURI(), $token->buffer()->getStartLine());
      
      $token->next();
      return self::handleAfter(new NewExpresion($name, $arg), $token);
    }
    
    if($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == "!"){
      $token->next();
      return new NotExpresion(self::is($token));
    }
    
    return self::is($token);
  }
  
  private static function is(Tokenizer $token) : IExpresion{
    $exp = self::primetiv($token);
    if($token->buffer()->type == TokenType::KEYWORD && $token->buffer()->context == "is"){
      if($token->next()->type != TokenType::IDENTIFY)
        throw new ScriptParseException("After keyword 'is' there must be a identify");
      $identify = $token->buffer()->context;
      $token->next();
      return new IsExpresion($exp, $identify);
    }
    return $exp;
  }
  
  private static function primetiv(Tokenizer $token) : IExpresion{
    $buffer = $token->buffer();
    $token->next();
    
    if($buffer->type == TokenType::BOOL)
      return new BoolExpresion($buffer->context);
    
    if($buffer->type == TokenType::NULL)
      return new NullExpresion();
    
    if($buffer->type == TokenType::PUNCTOR && $buffer->context == "[")
      return self::getArray($token);
    
    if($buffer->type == TokenType::PUNCTOR && $buffer->context == "("){
      $exp = self::expresion($token);
      if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ")")
        throw new ScriptParseException("Missing )", $token->buffer()->getURI(), $token->buffer()->getStartLine());
      $token->next();
      return $exp;
    }
    
    if($buffer->type == TokenType::STRING)
      return new StringExpresion($buffer->context);
    
    if($buffer->type == TokenType::NUMBER)
      return new NumberExpresion((float)$buffer->context);
    
    if($buffer->type == TokenType::IDENTIFY)
      return self::handleAfter(new IdentifyExpresion($buffer->context), $token);
    
    if($buffer->type == TokenType::KEYWORD && $buffer->context == "this")
      return self::handleAfter(new ThisExpresion(), $token);
    
    if($buffer->type == TokenType::KEYWORD && $buffer->context == "self")
      return self::handleAfter(new SelfExpresion(), $token);
    
    if($buffer->type == TokenType::KEYWORD && $buffer->context == "function")
      return self::handleAfter(new FunctionExpresion(FunctionBuilder::parseFunction($token, false)), $token);
    
    if($buffer->type == TokenType::KEYWORD && $buffer->context == "class")
      return self::handleAfter(new ClassExpresion(ClassBuilder::parseClass($token, false)), $token);
    
    throw new ScriptParseException("Unexpeted token detected {$buffer->context}({$buffer->type})", $buffer->getURI(), $buffer->getStartLine());
  }
  
  private static function handleAfter(IExpresion $expresion, Tokenizer $token) : IExpresion{
    $buffer = $token->buffer();
    if($buffer->type == TokenType::PUNCTOR){
      switch($buffer->context){
        case "[":
          return self::getArrayVar($expresion, $token);
        case "(":
          return self::getCall($expresion, $token);
        case "->":
          if($token->next()->type != TokenType::IDENTIFY)
            throw new ScriptParseException("After -> there must be a identify", $token->buffer()->getURI(), $token->buffer()->getStartLine());
          $name = $token->buffer()->context;
          if($token->next()->type == TokenType::PUNCTOR && $token->buffer()->context == "(")
            return self::handleAfter(self::getCall(new MethodExpresion($expresion, $name), $token), $token);
          return self::handleAfter(new PointerExpresion($expresion, $name), $token);
      }
    }
    return $expresion;
  }
  
  private static function getCall(IExpresion $func, Tokenizer $token) : IExpresion{
    $arg = [];
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != ")"){
      $arg[] = self::assign($token);
      while($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == ","){
        $token->next();
        $arg[] = self::assign($token);
      }
    }
    
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ")")
      throw new ScriptParseException("Missing ) ind end of call expresion", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    $token->next();
    return self::handleAfter(new CallExpresion($func, $arg), $token);
  }
  
  private static function getArrayVar(IExpresion $base, Tokenizer $token) : IExpresion{
    $key = null;
    if($token->next()->type != TokenType::PUNCTOR && $token->buffer()->context != "]"){
      $key = self::primetiv($token);
    }
    
    if($token->buffer()->type != TokenType::PUNCTOR && $token->buffer()->context != "]")
      throw new ScriptParseException("Expected ]", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    return self::handleAfter(new ArrayGetExpresion($base, $base), $token);
  }
  
  private static function getArray(Tokenizer $token) : ArrayExpresion{
    $context = [];
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != "]"){
      self::putArrayItem($context, $token);
      while($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == ","){
        $token->next();
        self::putArrayItem($context, $token);
      }
    }
    if($token->buffer()->type != TokenType::PUNCTOR && $token->buffer()->context != "]")
      throw new ScriptParseException("Unexpected token detected {$token->buffer()->context}({$token->buffer()->type})", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    return new ArrayExpresion($context);
  }
  
  private static function putArrayItem(array &$array, Tokenizer $token){
    $buffer = self::primetiv($token);
    if($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == ":"){
      $token->next();
      $array[] = [$buffer, self::primetiv($token)];
    }else
      $array[] = [null, $buffer];
  }
}