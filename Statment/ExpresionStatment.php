<?php
namespace Script\Statment;

use Script\Expresion\IExpresion;
use Script\ScriptContainer;
use Script\Complication\ComplicationResult;
use Script\Complication\ComplicationType;

class ExpresionStatment implements IStatment{
  private $expresion;
  
  public function __construct(IExpresion $expresion){
    $this->expresion = $expresion;
  }
  
  public function eval(ScriptContainer $container) : ComplicationResult{
    return new ComplicationResult(ComplicationType::NORMAL, $this->expresion->getValue($container));
  }
}