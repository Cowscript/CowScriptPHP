<?php
namespace Script\Token\Reader;

interface IReader{
  public function read() : int;
  public function peek() : int;
  public function getFile() : string;
  public function getLine() : int;
}