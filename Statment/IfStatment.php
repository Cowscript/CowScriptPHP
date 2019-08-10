<?php
namespace Script\Statment;

use Script\ScriptContainer;
use Script\ValueHandler;
use Script\Reference\ReferenceHandler;
use Script\Complication\ComplicationResult;
use Script\Complication\ComplicationType;
use Script\Program\IProgram;
use Script\Expresion\IExpresion;

class IfStatment implements IStatment{
  private $expresin;
  private $block;
  private $after;
  
  public function __construct(IExpresion $expresion, IProgram $program, ?IStatment $after){
    $this->expresion = $expresion;
    $this->block = $program;
    $this->after = $after;
  }
  
  public function eval(ScriptContainer $container) : ComplicationResult{
    if(ValueHandler::toBool(ReferenceHandler::getValue($this->expresion->getValue($container)))){
      return $this->block->eval($container);
    }
    
    if($this->after)
      return $this->after->eval($container);
    return new ComplicationResult(ComplicationType::NORMAL, null);
  }
}