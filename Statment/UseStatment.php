<?php
namespace Script\Statment;

use Script\ScriptContainer;
use Script\ValueHandler;
use Script\Complication\ComplicationResult;
use Script\Complication\ComplicationType;
use Script\Reference\ReferenceHandler;
use Script\Exception\ScriptRuntimeException;

class UseStatment implements IStatment{
  private $parts;
  
  public function __construct(array $parts){
    $this->parts = $parts;
  }
  
  function eval(ScriptContainer $container) : ComplicationResult{
    $last = null;
    for($i=0;$i<count($this->parts);$i++){
      $last = ValueHandler::toString(ReferenceHandler::getValue($this->parts[$i]->getValue($container)), $container);
      $last = $container->callPlugin($last);
    }
    return new ComplicationResult(ComplicationType::NORMAL, $last);
  }
}