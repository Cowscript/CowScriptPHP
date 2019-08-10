<?php
namespace Script\Statment;

use Script\ScriptContainer;
use Script\Complication\ComplicationResult;
use Script\Complication\ComplicationType;

class BreakStatment implements IStatment{
  public function eval(ScriptContainer $container) : ComplicationResult{
    return new ComplicationResult(ComplicationType::BREAK);
  }
}