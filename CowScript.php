<?php
namespace Script;

use Script\Program\IProgram;
use Script\Program\MainProgram;
use Script\Program\BlockProgram;
use Script\Token\Reader\StringReader;
use Script\Token\Reader\StreamReader;
use Script\Token\Tokenizer;
use Script\Token\TokenType;
use Script\SCallable\CallableValue;
use Script\SCallable\CallableMethod;
use Script\Exception\ScriptParseException;
use Script\Exception\ScriptRuntimeException;
use Script\Complication\ComplicationType;
use Script\Object\ClassContainer;
use Script\Object\ObjectContainer;
use Script\Object\CallableArgs;
use Script\SCallable\FunctionBuilder;
use Script\Statment\StatmentBuilder;

class CowScript{
  const VERSION = "V5.0";
  
  public function enableAutoload(){
    if(!defined("COWSCRIPT_AUTOLOAD")){
      define("COWSCRIPT_AUTOLOAD", dirname(__FILE__, 2)."/");
      spl_autoload_register(function($class){
        if(!class_exists($class) && strpos($class, "Script\\") == 0){
          include COWSCRIPT_AUTOLOAD.str_replace("\\", "/", $class).".php";
        }
      });
    }
  }
  
  public function getProgram($context) : IProgram{
    if(is_string($context))
      $reader = new StringReader($context);
    elseif(is_resource($context) && get_resource_type($context) == "stream")
      $reader = new StreamReader($context);
    
    $token   = new Tokenizer($reader);
    $program = [];
    
    //run to end of script
    while($token->buffer()->type != TokenType::EOF){
      $program[] = StatmentBuilder::getNextStatment($token);
    }
    
    return new MainProgram($program);
  }
  
  public function getModule(string $path){
    $token = new Tokenizer(new StreamReader(fopen($path, "r")));
    $container = new ScriptContainer();
    $return = [];
    while($token->buffer()->type != TokenType::EOF){
      if($token->buffer()->type == TokenType::KEYWORD && in_array($token->buffer()->context, ["function", "use", "class"])){
        StatmentBuilder::getNextStatment($token)->eval($container);
        continue;
      }elseif($token->buffer()->type == TokenType::KEYWORD && $token->buffer()->context == "public"){
        if($token->next()->type == TokenType::KEYWORD && in_array($token->buffer()->context, ["function", "class"])){
          $value = StatmentBuilder::getNextStatment($token)->eval($container)->value;
          if($value instanceof CallableValue){
            $return[$value->getName()] = $value;
          }else{
            $return[$value->getName()] = $value;
          }
          continue;
        }
      }
      throw new ScriptParseException("In module file the statments must start width function, use, class or public", $token->buffer()->getURI(), $token->buffer()->getStartLine());
    }
    return $return;
  }
}