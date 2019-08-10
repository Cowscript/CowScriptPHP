<?php
namespace Script\Exception;

class ScriptParseException extends ScriptException{
  public function __construct(string $message, string $file, int $startLine){
    $this->file = $file;
    $this->message = $message;
    $this->line = $startLine;
  }
}