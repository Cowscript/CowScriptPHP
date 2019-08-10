<?php
namespace Script\SCallable;

use Script\Token\Tokenizer;
use Script\Token\TokenType;
use Script\Object\CallableArgs;
use Script\Exception\ScriptParseException;
use Script\BlockBuilder;
use Script\Object\ClassContainer;
use Script\Object\ObjectContainer;
use Script\Reference\ReferenceHandler;
use Script\ScriptContainer;
use Script\Complication\ComplicationType;
use Script\Expresion\ExpresionEvulvor;
use Script\ValueHandler;

class ClassBuilder extends CallBuilder{
  public static function parseClass(Tokenizer $token, bool $names){
    if($names){
      if($token->next()->type != TokenType::IDENTIFY)
        throw new ScriptParseException("After the class keyword there must be a identify", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
      $name = $token->buffer()->context;
      $token->next();
    }else
      $name = "<inline class>";
    
    $item = ["pointer" => [], "method" => [], "extends" => null];
    $extends = null;
    if($token->buffer()->type == TokenType::KEYWORD && $token->buffer()->context == "extends"){
      if($token->next()->type != TokenType::IDENTIFY)
        throw new ScriptParseException("After extends keyword there must be a identify", $token->bufffer()->getURI(), $token->buffer()->getStartLine());
      $item["extends"] = $token->buffer()->context;
      $token->next();
    }
    
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != "{")
      throw new ScriptParseException("After class ".($names ? "name" : "keyword")." there must be a {", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    
    
    while($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != "}"){
      self::getClassAccessPart($item, $name, $token);
    }
    
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != "}")
      throw new ScriptParseException("Missing } in end of class body", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    $token->next();
    return [$name, $item];
  }
  
  public static function buildClass(array $data, ScriptContainer $container){
    $class = new ClassContainer($data[0]);
    self::setPointer($class, $data[1]["pointer"], $container);
    self::setMethod($class, $data[1]["method"], $container);
    if(!empty($data[1]["constructor"])){
      $names = $data[1]["constructor"][0];
      $block = $data[1]["constructor"][2];
      $class->setConstruct(new CallableMethod("", function(ScriptContainer $c, ObjectContainer $obj, array $arg) use($container, $names, $block){
        $container->pushStack($obj);
        for($i=0;$i<count($arg);$i++)
          $container->pushIdentify($names[$i], $arg[$i]);
        $block->eval($container);
        $container->popStack();
      }, $data[1]["constructor"][1]));
    }
    if($data[1]["extends"])
      $class->setExtends(ValueHandler::toClass($container->getIdentify($data[1]["extends"])));
    return $class;
  }
  
  private static function setPointer(ClassContainer $class, array $pointers, ScriptContainer $container){
    foreach($pointers as $pointer){
      $value = $pointer[1] === null ? null : ReferenceHandler::getValue($pointer[1]->getValue($container));
      if($pointer[3])
        $class->setStaticPointer($pointer[0], $value, $pointer[2]);
      else
        $class->setPointer($pointer[0], $value, $pointer[2]);
    }
  }
  
  private static function setMethod(ClassContainer $class, array $methods, ScriptContainer $container){
    foreach($methods as $method){
      $m = new CallableMethod($method[0], function(ScriptContainer $c, $obj, array $arg) use($container, $method){
        $container->pushStack($obj);
        for($i=0;$i<count($method[1]);$i++){
          $container->pushIdentify($method[1][$i], $arg[$i]);
        }
        $result = $method[3]->eval($container);
        $container->popStack();
        if($result->type == ComplicationType::RETURN)
          return $result->value;
        return null;
      });
      
      if($method[6] !== null)
        $m->setReturn($method[6]);
      
      if($method[5])
        $class->setStaticMethod($m, $method[4]);
      else
        $class->setMethod($m, $method[4]);
    }
  }
  
  private static function getClassAccessPart(array &$data, string $name, Tokenizer $token) : void{
    if($token->buffer()->type == TokenType::KEYWORD){
      switch($token->buffer()->context){
        case "public":
          $token->next();
          self::getClassTypePart($data, $token, true);
          return;
        case "private":
          $token->next();
          self::getClassTypePart($data, $token, false);
          return;
      }
    }
    if($token->buffer()->type == TokenType::IDENTIFY && $token->buffer()->context == $name){
      self::getConstructor($token, $data);
      return;
    }
    self::getClassTypePart($data, $token, true);
  }
  
  private static function getConstructor(Tokenizer $token, array &$data){
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != "(")
      throw new ScriptParseException("After constructor name there must be a (", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    $arg   = new CallableArgs();
    $names = [];
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != ")"){
      $names[] = self::getFuncArg($arg, $token);
      while($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == ","){
        $token->next();
        $names[] = self::getFuncArg($arg, $token);
      }
    }
    
    if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ")")
      throw new ScriptRuntimeException("After constructor arguments list there must be a )", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != "{")
      throw new ScriptRuntimeException("In begining af constructor body there must be a {", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    
    $data["constructor"] = [$names, $arg, BlockBuilder::getBlock($token)];
  }
  
  private static function getClassTypePart(array &$data, Tokenizer $token, bool $public) : void{
    if($token->buffer()->type == TokenType::KEYWORD && $token->buffer()->context == "static"){
      $token->next();
      self::getClassContext($data, $token, $public, true);
    }else
      self::getClassContext($data, $token, $public, false);
  }
  
  private static function getClassContext(array &$data, Tokenizer $token, bool $public, bool $static) : void{
    if($token->buffer()->type == TokenType::IDENTIFY){
      $name = $token->buffer()->context;
      if($token->next()->type == TokenType::PUNCTOR && $token->buffer()->context == "="){
        $token->next();
        $data["pointer"][] = [$name, ExpresionEvulvor::expresion($token), $public, $static];
      }else{
        $data["pointer"][] = [$name, null, $public, $static];
      }
      
      if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ";")
          throw new ScriptParseException("Unexpected token '{$token->buffer()->context}' detected after class pointer", $token->buffer()->getURI(), $token->buffer()->getStartLine());
      $token->next();  
    }elseif($token->buffer()->type == TokenType::KEYWORD && $token->buffer()->context == "function"){
      if($token->next()->type != TokenType::IDENTIFY)
        throw new ScriptParseException("After function keyword there must be a method name", $token->buffer()->getURI(), $token->buffer()->getStartLine());
      
      $name = $token->buffer()->context;
      $type = null;
      if($token->next()->type == TokenType::IDENTIFY){
        $type = $name;
        $name = $token->buffer()->context;
        $token->next();
      }
      
      if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != "(")
        throw new ScriptParseException("After method name there must be a (", $token->buffer()->getURI(), $token->buffer()->getStartLine());
      
      $arg   = new CallableArgs();
      $names = [];
      if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != ")"){
        $names[] = self::getFuncArg($arg, $token);
        while($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == ","){
          $token->next();
          $names[] = self::getFuncArg($arg, $token);
        }
      }
      
      if($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != ")")
        throw new ScriptParseException("Missing ) after method arguments list", $token->buffer()->getURI(), $token->buffer()->getStartLine());
      
      if($token->next()->type != TokenType::PUNCTOR || $token->buffer()->context != "{")
        throw new ScriptParseException("Missing { at start of method body", $token->buffer()->getURI(), $token->buffer()->getStartLine());
      
      $data["method"][] = [
        $name,
        $names,
        $arg,
        BlockBuilder::getBlock($token),
        $public, 
        $static,
        $type
        ];
    }else
      throw new ScriptParseException("Unexpected {$token->buffer()->context} in class body", $token->buffer()->getURI(), $token->buffer()->getStartLine());
  }
}