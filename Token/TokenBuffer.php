<?php
namespace Script\Token;

class TokenBuffer{
  private $type;
  private $context;
  private $uri;
  private $startLine;
  
  public function __construct(int $type, string $context, string $uri, int $startLine){
    $this->type      = $type;
    $this->context   = $context;
    $this->uri       = $uri;
    $this->startLine = $startLine;
  }
  
  public function getURI(): string{
    return $this->uri;
  }
  
  public function getStartLine() : int{
    return $this->startLine;
  }
  
  public function __get(string $key){
    switch($key){
      case "type":
        return $this->type;
      case "context":
        return $this->context;
    }
  }
}