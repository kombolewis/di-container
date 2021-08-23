<?php
namespace Core\Injector;

use ReflectionClass;
use Core\Injector\SharedService;
use Core\Exception\NotRegisteredException;

class Container
{
  private array $services = [];

  private static ?Container $container = null;

  public static function injector() :self {
    if(self::$container == null) {
      self::$container = new self;
    }
    return self::$container;
  }

  public static function setInjector(Container $container) :void{
    self::$container = $container;
  }
  /**
   *
   * @param string $name
   * @param mixed $service
   * @return void
   */
  public function register(string $name, $service) :void {
    $this->services[$name] = $service;
  }

  public function registerShared(string $name, $sharedService) :void {
    $this->register($name, new SharedService($name, $sharedService));
  }


  public function registered(string $name) :bool{
    return array_key_exists($name, $this->services);
  }

  /**
   * Undocumented function
   *
   * @param string $name
   * @return mixed
   */
  public function get(string $name) {
    if(!$this->registered($name))  {
      if(class_exists($name)) return $this->buildClass($name);
      throw new NotRegisteredException($name);
    }
    $service = $this->services[$name];

    if($service instanceof SharedService) {
      $sharedService = $service->sharedService;
      if(is_callable($sharedService)) {
        $sharedService = $sharedService($this);
      }
      $this->register($name, $sharedService);
      return $sharedService;
    }
    if(is_callable($service)) {
      return $service($this);
    }
    return $service;
  }

  public function registerCallable(string $name, Callable $callable) {
    $this->register($name, fn() => $callable);
  }

	protected function buildClass(string $name) :object {
    $reflectionClass = new ReflectionClass($name);
    $constructor = $reflectionClass->getConstructor();
    $parameters = $constructor ? $constructor->getParameters() :[];
    return new $name(...$this->getDependencies($parameters)); 
  }
  
  protected function getDependencies(array $parameters) :array{
    $dependecies = [];

    foreach($parameters as $parameter) {
      $position = $parameter->getPosition();
      $class = $parameter->getClass();
      $dependecies[$position] = $this->get($class->getName());
    }

    return $dependecies;
  }


}



