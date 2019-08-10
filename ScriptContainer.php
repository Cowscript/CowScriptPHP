<?php
namespace Script;

use Script\Identify\IdentifyContainer;
use Script\Plugin\IPlugin;
use Script\Plugin\ErrorPlugin;
use Script\Plugin\TypePlugin;
use Script\Plugin\ArrayPlugin;
use Script\Plugin\RegexPlugin;
use Script\Plugin\TimePlugin;
use Script\Plugin\RandomPlugin;
use Script\Plugin\ParserPlugin;
use Script\Plugin\MathPlugin;
use Script\SCallable\CallableValue;
use Script\Exception\ScriptRuntimeException;
use Script\Identify\ValueContainer;
use Script\Object\ClassContainer;
use Script\Item\StartItems;

class ScriptContainer{
  private $identifyStack = [];
  private $plugin = [];
  
  public function __construct(){
    $this->pushStack();
    StartItems::build($this);
    $this->pushPlugin("error",  new ErrorPlugin());
    $this->pushPlugin("type",   new TypePlugin());
    $this->pushPlugin("array",  new ArrayPlugin());
    $this->pushPlugin("regex",  new RegexPlugin());
    $this->pushPlugin("time",   new TimePlugin());
    $this->pushPlugin("random", new RandomPlugin());
    $this->pushPlugin("parser", new ParserPlugin());
    $this->pushPlugin("math",   new MathPlugin());
  }
  
  public function pushStack($owner = null){
    $this->identifyStack[] = new IdentifyContainer($owner);
  }
  
  public function popStack(){
    array_pop($this->identifyStack);
  }
  
  public function hasOwner() : bool{
    return $this->identifyStack[count($this->identifyStack) - 1]->hasOwner();
  }
  
  public function getOwner(){
    return $this->identifyStack[count($this->identifyStack) - 1]->getOwner();
  }
  
  public function hasIdentify(string $name) : bool{
    $length = count($this->identifyStack) - 1;
    for($i=$length;$i>=0;$i--){
      $current = $this->identifyStack[$i];
      if($current->hasIdentify($name)){
        return $i == $length || $current->getInformation($name)->isGlobal();
      }
    }
    
    return false;
  }
  
  public function getIdentify(string $name){
    $length = count($this->identifyStack) - 1;
    for($i=$length;$i>=0;$i--){
      $current = $this->identifyStack[$i];
      if($current->hasIdentify($name)){
        $info = $current->getInformation($name);
        if($i == $length)
          return $info->getValue();
        
        if($info->isGlobal())
          return $info->getValue();
      }
    }
    throw new ScriptRuntimeException("Unknown identify: ".$name);
  }
  
  public function pushFunction(CallableValue $func){
    $this->pushIdentify($func->getName(), $func);
    $info = $this->getInformation($func->getName());
    $info->setConst();//do not change this value or delete it
    $info->setGlobal();//let other function and so on use this function
  }
  
  public function pushClass(ClassContainer $class){
    $this->pushIdentify($class->getName(), $class);
    $info = $this->getInformation($class->getName());
    $info->setConst();
    $info->setGlobal();
  }
  
  public function getInformation(string $name) : ValueContainer{
    $length = count($this->identifyStack) - 1;
    for($i=$length;$i>=0;$i--){
      $current = $this->identifyStack[$i];
      if($current->hasIdentify($name))
        return $current->getInformation($name);
    }
  }
  
  public function pushIdentify(string $name, $value){
    if(!$this->hasIdentify($name)){
      $this->identifyStack[count($this->identifyStack) - 1]->pushIdentify($name, $value);
    }else{
      $length = count($this->identifyStack) - 1;
      for($i=$length;$i>=0;$i--){
        $current = $this->identifyStack[$i];
        if($current->hasIdentify($name)){
          $info = $current->getInformation($name);
          if($length > $i){
            if(!$info->isGlobal())
              return;
          }
          
          if($info->isConst())
            throw new ScriptRuntimeException("Cant overide a const identify '{$name}'");
          
          $current->pushIdentify($name, $value);
          return;
        }
        $this->identifyStack[$length]->pushIdentify($name, $value);
      }
    }
  }
  
  public function hasPlugin(string $name) : bool{
    return array_key_exists($name, $this->plugin);
  }
  
  public function pushPlugin(string $name, IPlugin $plugin){
    if(!$this->hasPlugin($name))
      $this->plugin[$name] = $plugin;
  }
  
  public function callPlugin(string $name) : bool{
    if($this->hasPlugin($name))
     return $this->plugin[$name]->set($this);
    
    //se if we got a files to make
    if(file_exists($name)){
      $handler = new CowScript();
      $module = $handler->getModule($name);
      foreach($module as $name => $value){
        if($value instanceof ClassContainer)
          $this->pushClass($value);
        else
          $this->pushFunction($value);
      }
      return true;
    }
    throw new ScriptRuntimeException("Unknown plugin: ".realpath($name));
  }
}