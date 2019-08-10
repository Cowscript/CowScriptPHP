<?php
namespace Script\Token;

class TokenType{
  const EOF      = 1;
  const IDENTIFY = 2;
  const PUNCTOR  = 3;
  const STRING   = 4;
  const NUMBER   = 5;
  const KEYWORD  = 6;
  const BOOL     = 7;
  const NULL     = 8;
}