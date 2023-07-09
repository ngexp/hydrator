<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\Between;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class BetweenTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_string_len_is_greater_than_min()
  {
    $attr = new Between(min: 2);
    $result = $attr->process($this->context("Hello"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_len_is_equal_to_min()
  {
    $attr = new Between(min: 2);
    $result = $attr->process($this->context("He"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_len_is_less_than_min()
  {
    $attr = new Between(min: 2);
    $result = $attr->process($this->context("H"));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_len_is_less_than_max()
  {
    $attr = new Between(max: 10);
    $result = $attr->process($this->context("Hello"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_len_is_equal_to_max()
  {
    $attr = new Between(max: 10);
    $result = $attr->process($this->context("Hello, wor"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_len_is_greater_than_max()
  {
    $attr = new Between(max: 10);
    $result = $attr->process($this->context("Hello, word!"));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_len_is_between_min_and_max_size()
  {
    $attr = new Between(min: 2, max: 10);
    $result = $attr->process($this->context("Hello"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_len_is_less_than_min_and_max_size()
  {
    $attr = new Between(min: 2, max: 10);
    $result = $attr->process($this->context("H"));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_len_is_greater_than_min_and_max_size()
  {
    $attr = new Between(min: 2, max: 10);
    $result = $attr->process($this->context("Hello, world!"));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_array_size_is_greater_than_min()
  {
    $attr = new Between(min: 2);
    $result = $attr->process($this->context([1, 2, 3]));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_array_size_is_equal_to_min()
  {
    $attr = new Between(min: 2);
    $result = $attr->process($this->context([1, 2]));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_array_size_is_less_than_min()
  {
    $attr = new Between(min: 2);
    $result = $attr->process($this->context([1]));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_array_size_is_less_than_max()
  {
    $attr = new Between(max: 10);
    $result = $attr->process($this->context([1, 2, 3, 4]));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_array_size_is_equal_to_max()
  {
    $attr = new Between(max: 10);
    $result = $attr->process($this->context([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_array_size_is_greater_than_max()
  {
    $attr = new Between(max: 10);
    $result = $attr->process($this->context([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_array_size_is_between_min_and_max_size()
  {
    $attr = new Between(min: 2, max: 10);
    $result = $attr->process($this->context([1, 2, 3, 4]));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_array_size_is_less_than_min_and_max_size()
  {
    $attr = new Between(min: 2, max: 10);
    $result = $attr->process($this->context([1]));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_array_size_is_greater_than_min_and_max_size()
  {
    $attr = new Between(min: 2, max: 10);
    $result = $attr->process($this->context([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]));

    $this->assertFalse($result->isValid());
  }
}
