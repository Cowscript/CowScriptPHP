<?php
namespace Script;

use Script\Token\Tokenizer;
use Script\Token\TokenType;
use Script\Program\BlockProgram;
use Script\Statment\StatmentBuilder;
use Script\Program\IProgram;

class BlockBuilder{
  public static function getBlock(Tokenizer $token) : IProgram{
    $program = new BlockProgram();
    if($token->buffer()->type == TokenType::PUNCTOR && $token->buffer()->context == "{"){
      $token->next();
      while(($token->buffer()->type != TokenType::PUNCTOR || $token->buffer()->context != "}") && $token->buffer()->type && $token->buffer()->type != TokenType::EOF){
        $program->add(StatmentBuilder::getNextStatment($token));
      }
      $token->next();
      return $program;
    }
    $program->add(StatmentBuilder::getNextStatment($token));
    return $program;
  }
}