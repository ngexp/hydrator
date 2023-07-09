<?php /** @noinspection PhpMultipleClassesDeclarationsInOneFile */

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\CustomConstraint;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class GreaterThan10 {
  public function __invoke(Context $context): Context
  {
    $value = $context->getValue();
    if ($value < 10) {
      return $context->withError("Less than 10");
    }

    return $context->asValid();
  }
}

class NoneInvokable {}

class CustomConstraintTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_custom_constraint_with_invokable_class(): void
  {
     $attr = new CustomConstraint(GreaterThan10::class);
     $context = $this->context(15);
     $context = $attr->process($context);

     $this->assertTrue($context->isValid());
  }

  /** @throws \Exception */
  public function test_custom_constraint_with_non_existing_class(): void
  {
    $attr = new CustomConstraint("SomeClass");
    $context = $this->context(15);
    $context = $attr->process($context);

    $this->assertFalse($context->isValid());
    $error = $context->getErrors()->first();
    $this->assertEquals(ErrorCode::CLASS_NAME, $error->getCode());
  }

  /** @throws \Exception */
  public function test_custom_hydrator_with_non_invokable_method(): void
  {
    $attr = new CustomConstraint(NoneInvokable::class);
    $context = $this->context(1);
    $context = $attr->process($context);

    $this->assertFalse($context->isValid());
    $error = $context->getErrors()->first();
    $this->assertEquals(ErrorCode::INVOKABLE, $error->getCode());
  }
}
