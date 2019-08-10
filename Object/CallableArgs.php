<?php
namespace Script\Object;

use Script\Reference\ReferenceHandler;
use Script\TypeHandler;
use Script\Exception\ScriptRuntimeException;
use Script\Expresion\IExpresion;
use Script\ScriptContainer;

class CallableArgs{
  private $arg = [];
  
  public function pushArg(?string $type, $value = null){
    $this->arg[] = [$type, $value];
  }
  
  public function count() : int{
    return count($this->arg);
  }
  
  public function resolveArguments(array $arg, string $name, ScriptContainer $container) : array{
    $args = [];
    $length = count($this->arg);
    for($i=0;$i<$length;$i++){
      if(!array_key_exists($i, $arg)){
        if($this->arg[$i][1] == null)
          throw new ScriptRuntimeException("Missing argument ".($i + 1)." on function call");
        if($this->arg[$i][1] instanceof IExpresion)
          $value = ReferenceHandler::getValue($this->arg[$i][1]->getValue($container));
        else
          $value = $this->arg[$i][1];
      }else{
        $value = $arg[$i];
      }
      
      $type = $this->arg[$i][0];
      if($type != null){
        $value = TypeHandler::convert($type, $value, $container);
      }
      $args[] = $value;
    }
    
    for(;$i<count($arg);$i++)
      $args[] = $arg[$i];
    
    return $args;
  }
}