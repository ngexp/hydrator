<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\Email;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class EmailTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_valid_email()
  {
    $attr = new Email();
    $result = $attr->process($this->context("john@doe.com"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_invalid_email()
  {
    $attr = new Email();
    $result = $attr->process($this->context("john_doe.com"));

    $this->assertFalse($result->isValid());
  }
}
