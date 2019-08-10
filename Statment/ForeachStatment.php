<?php
namespace Script\Statment;

use Script\ScriptContainer;
use Script\Complication\ComplicationType;
use Script\Complication\ComplicationResult;
use Script\Reference\ReferenceHandler;
use Script\Expresion\IExpresion;
use Script\Program\IProgram;
use Script\ValueHandler;

class ForeachStatment implements IStatment{
  private $expresion;
  private $identify;
  private $program;
  
  public function __construct(IExpresion $expresion, string $identify, IProgram $program){
    $this->expresion = $expresion;
    $this->identify  = $identify;
    $this->program   = $program;
  }
  
  public function eval(ScriptContainer $container) : ComplicationResult{
    $result = new ComplicationResult(ComplicationType::NORMAL);
    $array = ValueHandler::toArray(ReferenceHandler::getValue($this->expresion->getValue($container)));
    $length = count($array);
    for($i=0;$i<$length;$i++){
      $container->pushIdentify($this->identify, $array[$i]);
      $result = $this->program->eval($container);
      if($result->type == ComplicationType::RETURN)
        return $result;
      elseif($result->type == ComplicationType::CONTINUE)
        $result = new ComplicationResult(ComplicationType::NORMAL);
      elseif($result->type == ComplicationType::BREAK)
        return new ComplicationResult(ComplicationType::NORMAL);
    }
    return $result;
  }
}