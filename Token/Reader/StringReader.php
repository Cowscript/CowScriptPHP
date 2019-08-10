<?php
namespace Script\Token\Reader;

class StringReader implements IReader{
  private $pointer = 0;
  private $string;
  private $line = 1;
  
  public function __construct(string $context){
    $this->string = $context;
  }
  
  public function read() : int{
    if($this->IsEmpty())
      return -1;
    
    $c = $this->string[$this->pointer];
    $this->pointer++;
    if($c == '\n'){
      $this->line++;
    }
    return ord($c);
  }
  
  public function peek() : int{
    if($this->isEmpty())
      return -1;
    return ord($this->string[$this->pointer]);
  }
  
  public function getFile() : string{
    return "<string>";
  }
  
  public function getLine() : int{
   return $this->line; 
  }
  
  private function IsEmpty(){
    return strlen($this->string) <= $this->pointer;
  }
}