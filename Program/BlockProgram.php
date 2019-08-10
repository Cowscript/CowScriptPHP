<?php
namespace Script\Program;

use Script\ScriptContainer;
use Script\Complication\ComplicationResult;
use Script\Complication\ComplicationType;
use Script\Statment\IStatment;

class BlockProgram implements IProgram{
  private $statments = [];
  
  public function add(IStatment $statment){
    $this->statments[] = $statment;
  }
  
  function eval(ScriptContainer $container) : ComplicationResult{
    $last = new ComplicationResult(ComplicationType::NORMAL, null);
    for($i=0;$i<count($this->statments);$i++){
      $last = $this->statments[$i]->eval($container);
      if($last->type != ComplicationType::NORMAL)
        break;
    }
    return $last;
  }
}