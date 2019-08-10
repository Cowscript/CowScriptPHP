<?php
namespace Script\Program;

use Script\ScriptContainer;
use Script\Complication\ComplicationResult;

interface IProgram{
  function eval(ScriptContainer $container) : ComplicationResult;
}