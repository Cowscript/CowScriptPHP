<?php
namespace Script\Expresion;

use Script\ScriptContainer;

class NullExpresion implements IExpresion{
  public function getValue(ScriptContainer $container){
    return null;
  }
}