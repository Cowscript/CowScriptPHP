<?php
namespace Script\Item;

use Script\ScriptContainer;

class StartItems{
  public static function build(ScriptContainer $container){
    ScriptString::build($container);
    ScriptArray::build($container);
    ScriptInt::build($container);
  }
}