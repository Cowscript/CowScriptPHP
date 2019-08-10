<?php
namespace Script\Complication;

class ComplicationResult{
  public $type;
  public $value;
  
  public function __construct(int $type, $value = null){
    $this->type = $type;
    $this->value = $value;
  }
}