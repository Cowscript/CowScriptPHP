<?php
namespace Script\Statment;

use Script\ScriptContainer;
use Script\ValueHandler;
use Script\Complication\ComplicationResult;
use Script\Complication\ComplicationType;
use Script\Reference\ReferenceHandler;
use Script\Program\IProgram;
use Script\Expresion\IExpresion;

class WhileStatment implements IStatment{
  private $expresion;
  private $program;
  
  public function __construct(IExpresion $expresion, IProgram $program){
    $this->expresion = $expresion;
    $this->program   = $program;
  }
  
  public function eval(ScriptContainer $container) : ComplicationResult{
    $c = new ComplicationResult(ComplicationType::NORMAL);
    while(ValueHandler::toBool(ReferenceHandler::getValue($this->expresion->getValue($container)))){
      $c = $this->program->eval($container);
      if($c->type == ComplicationType::RETURN)
        return $c;
      elseif($c->type == ComplicationType::CONTINUE)
        $c = new ComplicationResult(ComplicationType::NORMAL);
      elseif($c->type == ComplicationType::BREAK)
        return new ComplicationResult(ComplicationType::NORMAL);
    }
    return $c;
  }
}