<?php
namespace Script\Object;

use Script\SCallable\CallableMethod;
use Script\ScriptContainer;
use Script\Expresion\IExpresion;
use Script\Exception\ScriptRuntimeException;

class ClassContainer{
  private $name;
  private $pointers = [];
  private $staticPointer = [];
  private $constructor;
  private $method = [];
  private $staticMethod = [];
  private $ex;
  
  public function __construct(string $name){
    $this->name = $name;
  }
  
  public function setExtends(ClassContainer $class){
    $this->ex = $class;
  }
  
  public function is(string $name) : bool{
    if($this->name == $name)
      return true;
    if($this->ex)
      return $this->ex->is($name);
    return false;
  }
  
  public function getName() : string{
    return $this->name;
  }
  
  public function setPointer(string $name, $value, bool $isPrivate){
    $this->pointers[$name] = [$value, $isPrivate];
  }
  
  public function setStaticPointer(string $name, $value, bool $isPrivate){
    $this->staticPointer[$name] = [$value, $isPrivate];
  }
  
  public function putStaticPointer(string $name, $value){
    $this->staticPointer[$name][0] = $value;
  }
  
  public function getStaticPointer(string $name, ScriptContainer $container){
    $value = $this->staticPointer[$name][0];
    if($value instanceof IExpresion){
      $value = $value->getValue($container);
      $this->staticPointer[$name] = $value;
    }
    return $value;
  }
  
  public function hasPointer(string $name) : bool{
    $value = array_key_exists($name, $this->pointers);
    if(!$value && $this->ex && $this->ex->hasPointer($name) && $this->ex->isPointerPublic($name))
      return true;
    return $value;
  }
  
  public function isPointerPublic(string $name) : bool{
    if(array_key_exists($name, $this->pointers))
      return $this->pointers[$name][1];
    if($this->ex)
      return $this->ex->isPointerPublic($name);
    return false;
  }
  
  public function hasStaticPointer(string $name) : bool{
    return array_key_exists($name, $this->staticPointer);
  }
  
  public function getPointer(string $name, ScriptContainer $container){              
	//the script lexer can handle this but not all devolper can remember this (I have done it some time now)
	if(!$this->hasPointer($name))
		throw new ScriptRuntimeException("Unknown pointer '{$name}' from '{$this->getName()}'");
	
    $value = $this->pointers[$name][0];
    if($value instanceof IExpresion)
      return $value->getValue($container);
    return $value;
  }
  
  public function setConstruct(CallableMethod $callable) : void{
    $this->constructor = $callable;
  }
  
  public function getConstruct() : CallableMethod{
    if(!$this->constructor)
      return $this->ex->getConstruct();
    return $this->constructor;
  }
  
  public function hasConstruct() : bool{
    if($this->constructor)
      return true;
    
    if($this->constructor == null && $this->ex != null)
      return $this->ex->hasConstruct();
    return false;
  }
  
  public function hasMethod(string $name) : bool{
    $value = array_key_exists($name, $this->method);
    if(!$value && $this->ex && $this->ex->hasMethod($name) && $this->ex->methodPublic($name))
      return true;
    return $value;
  }
  
  public function hasStaticMethod(string $name) : bool{
    return array_key_exists($name, $this->staticMethod);
  }
  
  public function setMethod(CallableMethod $method, bool $public){
    $this->method[$method->getName()] = [$method, $public];
  }
  
  public function setStaticMethod(CallableMethod $method, bool $public){
    $this->staticMethod[$method->getName()] = [$method, $public];
  }
  
  public function methodPublic(string $name) : bool{
    if(array_key_exists($name, $this->method))
       return $this->method[$name][1];
    return $this->ex->methodPublic($name);
  }
  
  public function staticMethodPublic(string $name) : bool{
    return $this->staticMethod[$name][1];
  }
  
  public function getMethod(string $name) : CallableMethod{
    if(!array_key_exists($name, $this->method) && $this->ex->hasMethod($name) && $this->ex->methodPublic($name))
      return $this->ex->getMethod($name);
    return $this->method[$name][0];
  }
  
  public function getStaticMethod(string $name) : CallableMethod{
    return $this->staticMethod[$name][0];
  }
}