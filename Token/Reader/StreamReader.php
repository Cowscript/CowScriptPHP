<?php
namespace Script\Token\Reader;

class StreamReader implements IReader{
  private $stream;
  private $line = 1;
  
  function __construct($stream){
    $this->stream = $stream;
  }
  
  function read() : int{
    $c = fgetc($this->stream);
    if($c === false)
      return -1;
    
    if($c == "\n")
      $this->line++;
    
    return ord($c);
  }
  
  function peek() : int{
    $c = fgetc($this->stream);
    fseek($this->stream, -1, SEEK_CUR);
    if($c === false)
      return -1;
    return ord($c);
  }
  
  function getFile() : string{
    return stream_get_meta_data($this->stream)["uri"];
  }
  
  function getLine() : int{
   return $this->line; 
  }
}