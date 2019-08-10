<?php
namespace Script\Statment;

use Script\ScriptContainer;
use Script\Complication\ComplicationResult;
use Script\Program\IProgram;

class ElseStatment implements IStatment{
  private $program;
  
  public function __construct(IProgram $program){
    $this->program = $program;
  }
  
  public function eval(ScriptContainer $container) : ComplicationResult{
    return $this->program->eval($container);
  }
}