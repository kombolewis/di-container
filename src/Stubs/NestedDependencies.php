<?php
namespace Core\Stubs;

class NestedDependencies
{
  private SingleDependency $singleDependency;

  private MultipleDependencies $multipleDependencies;
  
  public function __construct(SingleDependency $singleDependency, MultipleDependencies $multipleDependencies) {
    $this->singleDepency = $singleDependency;
    $this->multipleDependencies = $multipleDependencies;
  }

}

