<?php
namespace Script\Reference;

use Script\ScriptContainer;

class IdentifyReference implements IReference{
  private $container;
  private $name;
  
  public function __construct(string $name, ScriptContainer $container){
    $this->name = $name;
    $this->container = $container;
  }
  
  public function put($value){
    $this->container->pushIdentify($this->name, $value);
  }
  
  public function getValue(){
    return $this->container->getIdentify($this->name);
  }
}