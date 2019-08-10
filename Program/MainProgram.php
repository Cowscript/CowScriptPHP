<?php
namespace Script\Program;

use Script\ScriptContainer;
use Script\Complication\ComplicationResult;
use Script\Complication\ComplicationType;

class MainProgram implements IProgram{
  private $item;
  
  public function __construct(array $data){
    $this->item = $data;
  }
  
  public function eval(ScriptContainer $container) : ComplicationResult{
    $com = new ComplicationResult(ComplicationType::NORMAL);
    foreach($this->item as $statment){
      $com = $statment->eval($container);
    }
    return $com;
  }
}