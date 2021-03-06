<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Constraints;

use Ngexp\Hydrator\Constraints\Graph;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class GraphTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_valid_graph()
  {
    $attr = new Graph();
    $result = $attr->constraint($this->context("ABC123"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_invalid_graph()
  {
    $attr = new Graph();
    $result = $attr->constraint($this->context("ABC\t\n"));

    $this->assertFalse($result->isValid());
  }
}
