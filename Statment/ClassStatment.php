<?php
namespace Script\Statment;

use Script\ScriptContainer;
use Script\Complication\ComplicationResult;
use Script\Complication\ComplicationType;
use Script\SCallable\ClassBuilder;

class ClassStatment implements IStatment{
  private $data;
  
  public function __construct(array $data){
    $this->data = $data;
  }
  
  public function eval(ScriptContainer $container) : ComplicationResult{
    $class = ClassBuilder::buildClass($this->data, $container);
    $container->pushClass($class);
    return new ComplicationResult(ComplicationType::NORMAL, $class);
  }
}