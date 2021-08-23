<?php
namespace Core\Stubs;

class SingleDependency
{ 
  private NoConstructor $noConstructor;

  public function __construct(NoConstructor $noConstructor)
  {
    $this->noConstructor = $noConstructor;
  }
}

