<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\Moment;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class MomentTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_valid_date(): void
  {
    $attr = new Moment();
    $result = $attr->process($this->context("2023-02-03"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_invalid_date(): void
  {
    $attr = new Moment();
    $result = $attr->process($this->context("23-02-03"));

    $this->assertFalse($result->isValid());
  }
}
