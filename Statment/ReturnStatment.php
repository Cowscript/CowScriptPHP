<?php
namespace Script\Statment;

use Script\ScriptContainer;
use Script\Complication\ComplicationResult;
use Script\Complication\ComplicationType;
use Script\Expresion\IExpresion;
use Script\Reference\ReferenceHandler;

class ReturnStatment implements IStatment{
  private $return;
  
  public function __construct(IExpresion $expresion){
    $this->return = $expresion; 
  }
  
  public function eval(ScriptContainer $container) : ComplicationResult{
    return new ComplicationResult(ComplicationType::RETURN, ReferenceHandler::getValue($this->return->getValue($container)));
  }
}