<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\Graph;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class GraphTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_valid_graph()
  {
    $attr = new Graph();
    $result = $attr->process($this->context("ABC123"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_invalid_graph()
  {
    $attr = new Graph();
    $result = $attr->process($this->context("ABC\t\n"));

    $this->assertFalse($result->isValid());
  }
}
