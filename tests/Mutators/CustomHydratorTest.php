<?php /** @noinspection PhpMultipleClassesDeclarationsInOneFile */

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\Mutators\CustomHydrator;
use Ngexp\Hydrator\Tests\AttributeTestCase;
use Ngexp\Hydrator\Type;

class IncreaseByOneTest {
  public function __invoke(Context $context): Context
  {
    $value = $context->getValue();
    $context->withValue($value + 1);
    return $context;
  }
}

class NoneInvokable {}

class CustomHydratorTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_custom_hydrator_with_invokable_class(): void
  {
    $attr = new CustomHydrator(IncreaseByOneTest::class);
    $context = $this->context(1);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::INT, $this->getVariableType($result));
    $this->assertEquals(2, $result);
  }

  /** @throws \Exception */
  public function test_custom_hydrator_with_non_existing_class(): void
  {
    $attr = new CustomHydrator("SomeClass");
    $context = $this->context(1);
    $context = $attr->process($context);

    $this->assertFalse($context->isValid());
    $error = $context->getErrors()->first();
    $this->assertEquals(ErrorCode::CLASS_NAME, $error->getCode());
  }

  /** @throws \Exception */
  public function test_custom_hydrator_with_non_invokable_method(): void
  {
    $attr = new CustomHydrator(NoneInvokable::class);
    $context = $this->context(1);
    $context = $attr->process($context);

    $this->assertFalse($context->isValid());
    $error = $context->getErrors()->first();
    $this->assertEquals(ErrorCode::INVOKABLE, $error->getCode());
  }
}
