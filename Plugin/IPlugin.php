<?php
namespace Script\Plugin;

use Script\ScriptContainer;

interface IPlugin{
  function set(ScriptContainer $container) : bool;
}