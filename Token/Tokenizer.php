<?php
namespace Script\Token;

use Script\Token\Reader\IReader;
use Script\Exception\ScriptParseException;

class Tokenizer{
  private $reader;
  private $buffer;
  private $uri;
  private $startLine = -1;
  private $endLine = -1;
  
  public function __construct(IReader $reader){
    $this->reader = $reader;
    $this->uri = $this->reader->getFile();
    $this->next();//make new buffer becuse current it empty
  }
  
  public function next() : TokenBuffer{
    return $this->buffer = $this->generate();
  }
  
  public function buffer() : TokenBuffer{
    return $this->buffer;
  }
  
  private function generate() : TokenBuffer{
    $c = $this->gc();
    if($c == -1)
      return $this->makeBuffer(TokenType::EOF, "End of file");
    
    if($c == 34 || $c == 39)
      return $this->string($c);
    
    if(ScriptChar::identifyStart($c))
      return $this->getIdentify($c);
    
    if(ScriptChar::numeric($c))
      return $this->getNumber($c);
    
    return $this->punctor($c);
  }
  
  private function getNumber(int $number) : TokenBuffer{
    $number = chr($number).$this->number();
    if($this->reader->peek() == 46){
      $this->reader->read();
      $number .= ".".$this->number();
    }
    
    return $this->makeBuffer(TokenType::NUMBER, $number);
  }
  
  private function number() : string{
    $return = "";
    while(ScriptChar::numeric($this->reader->peek()))
      $return .= chr($this->reader->read());
    return $return;
  }
  
  private function string(int $end){
    $context = "";
    while(true){
      $c = $this->reader->read();
      
      if($c == -1)
        throw new ScriptParseException("Missing ".chr($end).". Got end of script", $this->uri, $this->startLine);
      
      if($end == $c)
        break;
      
      $context .= chr($c);
    }
    return $this->makeBuffer(TokenType::STRING, $context);
  }
  
  private function getIdentify(string $start){
    $str = chr($start);
    
    while(ScriptChar::identifyPart($this->reader->peek()))
      $str .= chr($this->reader->read());
    
    switch($str){
      case "true":
      case "false":
        return $this->makeBuffer(TokenType::BOOL, $str);
      case "null":
        return $this->makeBuffer(TokenType::NULL, "null");
      case "use":
      case "func":
      case "function":
      case "if":
      case "elseif":
      case "else":
      case "return":
      case "class":
      case "public":
      case "private":
      case "static":
      case "new":
      case "this":
      case "self":
      case "foreach":
      case "as":
      case "for":
      case "is":
      case "while":
      case "continue":
      case "break":
      case "extends":
        return $this->makeBuffer(TokenType::KEYWORD, $str);
    }
    
    return $this->makeBuffer(TokenType::IDENTIFY, $str);
  }
  
  private function punctor(int $c){
    switch($c){
      case 33:
        if($this->reader->peek() == 61){
          $this->reader->read();
          return $this->makeBuffer(TokenType::PUNCTOR, "!=");
        }
        return $this->makeBuffer(TokenType::PUNCTOR, "!");
      case 38:
        if($this->reader->peek() == 38){
          $this->reader->read();
          return $this->makeBuffer(TokenType::PUNCTOR, "&&");
        }
        return $this->makeBuffer(TokenType::PUNCTOR, "&");
      case 40:
        return $this->makeBuffer(TokenType::PUNCTOR, "(");
      case 41:
        return $this->makeBuffer(TokenType::PUNCTOR, ")");
      case 42:
        return $this->makeBuffer(TokenType::PUNCTOR, "*");
      case 43:
        if($this->reader->peek() == 43){
          $this->reader->read();
          return $this->makeBuffer(TokenType::PUNCTOR, "++");
        }
        return $this->makeBuffer(TokenType::PUNCTOR, "+");
      case 44:
        return $this->makeBuffer(TokenType::PUNCTOR, ",");
      case 45:
        if($this->reader->peek() == 45){
          $this->reader->read();
          return $this->makeBuffer(TokenType::PUNCTOR, "--");
        }elseif($this->reader->peek() == 62){
          $this->reader->read();
          return $this->makeBuffer(TokenType::PUNCTOR, "->");
        }
        return $this->makeBuffer(TokenType::PUNCTOR, "-");
      case 47:
        return $this->makeBuffer(TokenType::PUNCTOR, "/");
      case 59:
        return $this->makeBuffer(TokenType::PUNCTOR, ";");
      case 58:
        return $this->makeBuffer(TokenType::PUNCTOR, ":");
      case 60:
        if($this->reader->peek() == 61){
          $this->reader->read();
          return $this->makeBuffer(TokenType::PUNCTOR, "<=");
        }
        return $this->makeBuffer(TokenType::PUNCTOR, "<");
      case 61:
        if($this->reader->peek() == 61){
          $this->reader->read();
          return $this->makeBuffer(TokenType::PUNCTOR, "==");
        }
        return $this->makeBuffer(TokenType::PUNCTOR, "=");
      case 62:
        if($this->reader->peek() == 61){
          $this->reader->read();
          return $this->makeBuffer(TokenType::PUNCTOR, ">=");
        }
        return $this->makeBuffer(TokenType::PUNCTOR, ">");
      case 63:
        return $this->makeBuffer(TokenType::PUNCTOR, "?");
      case 91:
        return $this->makeBuffer(TokenType::PUNCTOR, "[");
      case 93:
        return $this->makeBuffer(TokenType::PUNCTOR, "]");
      case 94:
        return $this->makeBuffer(TokenType::PUNCTOR, "^");
      case 123:
        return $this->makeBuffer(TokenType::PUNCTOR, "{");
      case 124:
        if($this->reader->peek() == 124){
          $this->reader->read();
          return $this->makeBuffer(TokenType::PUNCTOR, "||");
        }
        return $this->makeBuffer(TokenType::PUNCTOR, "|");
      case 125:
        return $this->makeBuffer(TokenType::PUNCTOR, "}");
    }
    throw new ScriptParseException("Unknown char detected ".chr($c)."({$c})", $this->uri, $this->startLine);
  }
  
  private function makeBuffer(int $type, string $context) : TokenBuffer{
    return new TokenBuffer($type, $context, $this->uri, $this->startLine);
  }
  
  private function gc() : int{
    $c = $this->reader->read();
    $this->startLine = $this->reader->getLine();
    if($c == -1)
      return -1;
    
    if($c == 9 || $c == 10 || $c == 13 || $c == 32)
      return $this->gc();
    
    if($c == 47){
      if($this->reader->peek() == 47){
        while(($c = $this->reader->read()) != -1 && $c != 10) ;
        return $this->gc();
      }
      
      if($this->reader->peek() == 42){
        $this->reader->read();
        while(($c = $this->reader->read()) != -1){
          if($c == 42 && $this->reader->peek() == 47){
            $this->reader->read();
            return $this->gc();
          }
        }
        throw new ScriptParseException("Missing end of block comments '*/'", $this->reader->getURI(), $this->reader->startLine);
      }
    }
    
    if($c == 35){
      while(($c = $this->reader->read()) != -1 && $c != 10) ;
      return $this->gc();
    }
    
    return $c;
  }
}