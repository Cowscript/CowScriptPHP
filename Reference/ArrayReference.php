<?php
namespace Script\Reference;

class ArrayReference implements IReference{
  private $array;
  private $key;
  
  public function __construct(array $array, ?int $key){
    $this->array = $array;
    $this->key = $key;
  }
  
  public function put($value){
    if($this->key == null){
      $this->array[] = $value;
      return;
    }
    exit("Put array");
  }
  
  public function getValue(){
    exit("Get array");
  }
}