<?php
namespace Tests\Unit;

use Closure;
use PHPUnit\Framework\TestCase;

use Core\Injector\Container;
use Core\Stubs\NoConstructor;
use Core\Stubs\EmptyConstructor;
use Core\Stubs\SingleDependency;
use Core\Stubs\NestedDependencies;
use Core\Stubs\MultipleDependencies;
use Core\Exception\NotRegisteredException;

class ContainerTest extends TestCase
{
  /**
   * @test
   */
  public function it_can_be_created() {
    $this->assertNotNull(new Container);
  }

  /**
   * @test
   */
  public function can_register_services() {
    $injector = new Container;
    $injector->register('foo','this is foo');
    $this->assertTrue($injector->registered('foo'));
  }
  
  /**
   * @test
   */
  public function can_get_services_that_are_registered() {
    $injector = new Container;
    $injector->register('foo', 'this is foo');
    $this->assertEquals('this is foo', $injector->get('foo'));

  }

  /**
   * @test
   */
  public function throws_not_registered_exception_when_getting_non_registered_items(){
    $this->expectException(NotRegisteredException::class);
    $this->expectExceptionMessage("`not registered` is not registered with this container");
    $injector = new Container;
    $injector->get('not registered');
  }

  /**
   * @test
   */
  public function can_be_a_singleton() {
    $this->assertInstanceOf(Container::class, Container::injector());
    $this->assertSame(Container::injector(), Container::injector());
  }

  /**
   * @test
   */
  public function allows_singleton_to_be_set() {
    $container = new Container;
    $container::setInjector($container);
    $this->assertSame($container, Container::injector());
  }

  /**
   * @test 
   */
  public function evaluates_functions_that_are_registered_as_services() {
    $injector = new Container;
    $injector->register('foo', fn() => 'this is foo');
    $this->assertEquals('this is foo', $injector->get('foo'));
  }

  /**
   * @test
   */
  public function passes_itself_into_functions_that_are_registered_as_services() {
    $injector = new Container;
    $injector->register('foo', function(?Container $container = null) use($injector) {
      $this->assertSame($injector, $container);
    });
    $injector->get('foo');
  }

  /**
   * @test
   */
  public function can_instatiate_classes_that_are_not_registered_with_it() {
    $injector = new Container;
    $this->assertInstanceOf(Container::class, $injector->get(Container::class));

  }

  /**
   * @test
   * 
   * @dataProvider providesClassesToInstantiate
   */
  public function can_resolve_dependencies_when_instatiating_classes_that_are_not_registered($expectedInstance) {
    $injector = new Container;
    $this->assertInstanceOf($expectedInstance, $injector->get($expectedInstance));
  }


  /**
   * @test
   * 
   * @dataProvider providesCallables
   */
  public function can_register_callables_that_arent_evaluated($callable, $expectedOutput) {
    $injector = new Container;
    $injector->registerCallable('someCallable', $callable);
    $this->assertEquals($expectedOutput, $injector->get('someCallable')());
  }

  /**
   * @test
   *
   */
  public function can_register_services_to_be_shared() {
    $injector = new Container;
    $injector->registerShared(NoConstructor::class, fn () => new NoConstructor);
    $this->assertInstanceOf(NoConstructor::class, $injector->get(NoConstructor::class));
    $this->assertSame($injector->get(NoConstructor::class), $injector->get(NoConstructor::class));
  }

  public function providesCallables() {
    return [
      'anonymus function' => [fn() => 'this is someFunction', 'this is someFunction'],
      'invokable class' => [
        new class {
          public function __invoke() {
            return 'some invokable class';
          }
        },
        'some invokable class'
      ],
      'closure' => [Closure::fromCallable(fn() => 'some closure'),'some closure']
    ];
  }

	public function providesClassesToInstantiate() {
    return [
      [NoConstructor::class],
      [EmptyConstructor::class],
      [SingleDependency::class],
      [MultipleDependencies::class],
      [NestedDependencies::class],
    ];
	}
}

