<?php
namespace Script\Statment;

use Script\ScriptContainer;
use Script\Complication\ComplicationResult;

interface IStatment{
  function eval(ScriptContainer $container) : ComplicationResult;
}