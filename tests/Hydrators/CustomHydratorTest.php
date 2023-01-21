<?php /** @noinspection PhpMultipleClassesDeclarationsInOneFile */

declare(strict_types=1);


namespace Ngexp\Hydrator\Tests\Hydrators;

use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\Hydrators\CustomHydrator;
use Ngexp\Hydrator\Tests\AttributeTestCase;
use Ngexp\Hydrator\Type;

class IncreaseByOneTest {
  public function __invoke(Context $context): Context
  {
    $value = $context->getValue();
    $context->setValue($value + 1);
    return $context;
  }
}

class NoneInvokable {}

class CustomHydratorTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_custom_hydrator_with_invokable_class()
  {
    $attr = new CustomHydrator(IncreaseByOneTest::class);
    $context = $this->context(1);
    $context = $attr->hydrateValue($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::INT, $this->getVariableType($result));
    $this->assertEquals(2, $result);
  }

  /** @throws \Exception */
  public function test_custom_hydrator_with_non_existing_class()
  {
    $attr = new CustomHydrator("SomeClass");
    $context = $this->context(1);
    $context = $attr->hydrateValue($context);

    $this->assertFalse($context->isValid());
    $failure = $context->getFailureMessages()[0];
    $this->assertEquals(CustomHydrator::NOT_A_CLASS, $failure->getErrorCode());
  }

  /** @throws \Exception */
  public function test_custom_hydrator_with_non_invokable_method()
  {
    $attr = new CustomHydrator(NoneInvokable::class);
    $context = $this->context(1);
    $context = $attr->hydrateValue($context);

    $this->assertFalse($context->isValid());
    $failure = $context->getFailureMessages()[0];
    $this->assertEquals(CustomHydrator::NOT_INVOKABLE, $failure->getErrorCode());
  }
}
