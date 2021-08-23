<?php
namespace Core\Stubs;

class MultipleDependencies
{
  private EmptyConstructor $emptyConstructor;

  private NoConstructor $noConstructor;

  public function __construct(EmptyConstructor $emptyConstructor, NoConstructor $noConstructor) {
    $this->emptyConstructor = $emptyConstructor;
    $this->noConstructor = $noConstructor;
  }

}

