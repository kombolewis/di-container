<?php
namespace Core\Injector;

class SharedService
{
  public string $name;

  /**
   *
   * @var mixed $sharedService
   */
  public $sharedService;

  public function __construct(string $name, $sharedService) {
    $this->name = $name;
    $this->sharedService = $sharedService;
  }
}

