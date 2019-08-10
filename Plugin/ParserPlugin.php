<?php
namespace Script\Plugin;

use Script\ScriptContainer;
use Script\SCallable\CallableValue;
use Script\CowScript;

class ParserPlugin implements IPlugin{
  public function set(ScriptContainer $container) : bool{
    $container->pushFunction(new CallableValue("version", function(ScriptContainer $container, array $arg){
      return CowScript::VERSION;
    }));
    return true;
  }
}