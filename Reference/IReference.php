<?php
namespace Script\Reference;

use Script\ScriptContainer;

interface IReference{
  function put($value);
  function getValue();
}