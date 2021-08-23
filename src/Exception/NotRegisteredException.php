<?php
namespace Core\Exception;

use RuntimeException;

class NotRegisteredException extends RuntimeException
{
  public function __construct(string $name) 
  {
    $message = sprintf("`%s` is not registered with this container", $name);
    parent::__construct($message);
    
  }
}

