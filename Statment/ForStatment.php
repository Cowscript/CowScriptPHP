<?php
namespace Script\Statment;

use Script\ScriptContainer;
use Script\Expresion\IExpresion;
use Script\Program\IProgram;
use Script\Complication\ComplicationResult;
use Script\Complication\ComplicationType;
use Script\ValueHandler;

class ForStatment implements IStatment{
  private $first;
  private $second;
  private $last;
  private $program;
  
  public function __construct(IExpresion $first, IExpresion $second, IExpresion $last, IProgram $program){
    $this->first   = $first;
    $this->second  = $second;
    $this->last    = $last;
    $this->program = $program;
  }
  
  public function eval(ScriptContainer $container) : ComplicationResult{
    $c = new ComplicationResult(ComplicationType::NORMAL);
    $this->first->getValue($container);
    while(ValueHandler::toBool($this->second->getValue($container))){
      $c = $this->program->eval($container);
      if($c->type == ComplicationType::RETURN)
        return $c;
      elseif($c->type == ComplicationType::CONTINUE)
        $c = new ComplicationResult(ComplicationType::NORMAL);
      elseif($c->type == ComplicationType::BREAK)
        return new ComplicationResult(ComplicationType::NORMAL);
      $this->last->getValue($container);
    }
    return $c;
  }
}