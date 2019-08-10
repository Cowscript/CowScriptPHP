<?php
namespace Script\SCallable;

use Script\ScriptContainer;
use Script\Object\CallableArgs;
use Script\TypeHandler;
use Script\Exception\ScriptRuntimeException;

class CallableValue{
  protected $name;
  protected $callable;
  protected $type;
  private $args;
  
  public function __construct(string $name, callable $callable, ?CallableArgs $arg = null){
    $this->name     = $name;
    $this->callable = $callable;
    $this->args = $arg == null ? new CallableArgs() : $arg;
  }
  
  public function call(ScriptContainer $container, array $arg){
    $return = call_user_func($this->callable, $container, $arg);
    if($this->type !== null){
      $return = TypeHandler::convert($this->type, $return, $container);
    }
    return $return;
  }
  
  public function pushArg(?string $type, $default = null){
    $this->args->pushArg($type, $default);
  }
  
  public function getArg() : CallableArgs{
    return $this->args;
  }
  
  public function getName() : string{
    return $this->name;
  }
  
  public function setReturn(string $type){
    $this->type = $type;
  }
  
  public function getReturn() : ?string{
    return $this->type;
  }
  
  public function resolveArguments(array $argument, ScriptContainer $container) : array{
    return $this->args->resolveArguments($argument, $this->name, $container);
  }
}