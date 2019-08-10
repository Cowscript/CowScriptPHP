<?php
namespace Script\SCallable;

use Script\ScriptContainer;
use Script\TypeHandler;
use Script\Exception\ScriptRuntimeException;

class CallableMethod extends CallableValue{
  public function methodCall(ScriptContainer $container, $owner, array $arg){
    $return = call_user_func($this->callable, $container, $owner, $arg);
    if($this->type !== null){
      $return = TypeHandler::convert($this->type, $return, $container);
    }
    return $return;
  }
}