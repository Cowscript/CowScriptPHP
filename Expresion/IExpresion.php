<?php
namespace Script\Expresion;

use Script\ScriptContainer;

interface IExpresion{
  function getValue(ScriptContainer $container);
}