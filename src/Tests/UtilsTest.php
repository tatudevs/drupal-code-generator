<?php

namespace DrupalCodeGenerator\Tests;

use DrupalCodeGenerator\Utils;
use PHPUnit\Framework\TestCase;

/**
 * Tests for a Utils class.
 */
class UtilsTest extends TestCase {

  /**
   * Test callback.
   *
   * @covers Utils::defaultPluginId
   * @dataProvider defaultPluginIdProvider
   */
  public function testDefaultPluginId($machine_name, $plugin_label, $expected) {
    $vars = [
      'machine_name' => $machine_name,
      'plugin_label' => $plugin_label,
    ];
    $this->assertEquals($expected, Utils::defaultPluginId($vars));
  }

  /**
   * Data provider callback for testDefaultPluginId().
   */
  public function defaultPluginIdProvider() {
    return [
      ['foo', 'Hello world', 'foo_hello_world'],
      ['bar', 'Ok*123', 'bar_ok_123'],
      ['abc', ' Hello world - %^$&*(^()& !', 'abc_hello_world'],
    ];
  }

  /**
   * Test callback.
   *
   * @covers Utils::machine2human
   * @dataProvider machineToHumanProvider
   */
  public function testMachineToHuman($machine_name, $expected) {
    $this->assertEquals($expected, Utils::machine2human($machine_name));
  }

  /**
   * Data provider callback for testMachineToHuman().
   */
  public function machineToHumanProvider() {
    return [
      ['hello_world', 'Hello world'],
      ['_hello_world_', 'Hello world'],
      ['__123', '123'],
    ];
  }

  /**
   * Test callback.
   *
   * @covers Utils::human2machine
   * @dataProvider human2machineProvider
   */
  public function testHumanToMachine($human_name, $expected) {
    $this->assertEquals($expected, Utils::human2machine($human_name));
  }

  /**
   * Data provider callback for testMachineToHuman().
   */
  public function human2machineProvider() {
    return [
      ['Hello world!', 'hello_world'],
      ['Camel Case Here', 'camel_case_here'],
      [' &*^*()@#a*&)(&*0b@#$$() c  !', 'a_0b_c'],
    ];
  }

  /**
   * Test callback.
   *
   * @covers Utils::camelize
   * @dataProvider camelizeProvider
   */
  public function testCamelize($string, $upper_camel, $expected) {
    $this->assertEquals($expected, Utils::camelize($string, $upper_camel));
  }

  /**
   * Data provider callback for testHuman2class().
   */
  public function camelizeProvider() {
    return [
      ['Hello world!', TRUE, 'HelloWorld'],
      ['snake_case_here', TRUE, 'SnakeCaseHere'],
      ['snake_case_here', FALSE, 'snakeCaseHere'],
      ['foo', TRUE, 'Foo'],
      [' &*^*()@#a*&)(&*0b@#$$() c  ! ', TRUE, 'a0bC'],
    ];
  }

  /**
   * Test callback.
   *
   * @param string $machine_name
   *   Machine name to validate.
   * @param \UnexpectedValueException|null $exception
   *   Expected exception.
   *
   * @covers Utils::validateMachineName
   * @dataProvider validateMachineNameProvider
   */
  public function testValidateMachineName($machine_name, $exception) {
    if ($exception) {
      $this->expectException(get_class($exception));
      $this->expectExceptionMessage($exception->getMessage());
    }
    $this->assertEquals($machine_name, Utils::validateMachineName($machine_name));
  }

  /**
   * Data provider callback for testValidateMachineName().
   *
   * @return array
   *   Array of arguments of test callback.
   */
  public function validateMachineNameProvider() {
    $exception = new \UnexpectedValueException('The value is not correct machine name.');
    return [
      ['snake_case_here', NULL],
      [' not_trimmed ', $exception],
      ['Hello world ', $exception],
      ['foo_', $exception],
      ['', $exception],
      ['foo*&)(*&@#()*&@#bar', $exception],
    ];
  }

  /**
   * Test callback.
   *
   * @param string $class_name
   *   Class name to validate.
   * @param \UnexpectedValueException|null $exception
   *   Expected exception.
   *
   * @covers Utils::validateClassName
   * @dataProvider validateClassNameProvider
   */
  public function testValidateClassName($class_name, $exception) {
    if ($exception) {
      $this->expectException(get_class($exception));
      $this->expectExceptionMessage($exception->getMessage());
    }
    $this->assertEquals($class_name, Utils::validateClassName($class_name));
  }

  /**
   * Data provider callback for testValidateClassName().
   *
   * @return array
   *   Array of arguments of test callback.
   */
  public function validateClassNameProvider() {
    $exception = new \UnexpectedValueException('The value is not correct class name.');
    return [
      ['GoodClassName', NULL],
      ['snake_case_here', $exception],
      [' NotTrimmed ', $exception],
      ['With_Underscore', $exception],
      ['WrongSymbols@)@#&)', $exception],
    ];
  }

  /**
   * Test callback.
   *
   * @param mixed $value
   *   The value to validate.
   * @param \UnexpectedValueException|null $exception
   *   Expected exception.
   *
   * @covers Utils::validateRequired
   * @dataProvider validateRequiredProvider
   */
  public function testValidateRequired($value, $exception) {
    if ($exception) {
      $this->expectException(get_class($exception));
      $this->expectExceptionMessage($exception->getMessage());
    }
    $this->assertEquals($value, Utils::validateRequired($value));
  }

  /**
   * Data provider callback for testValidateRequired().
   *
   * @return array
   *   Array of arguments of test callback.
   */
  public function validateRequiredProvider() {
    $exception = new \UnexpectedValueException('The value is required.');
    return [
      ['yes', NULL],
      ['0', NULL],
      [0, NULL],
      [FALSE, NULL],
      ['', $exception],
      [NULL, $exception],
    ];
  }

  /**
   * Test callback.
   *
   * @covers Utils::normalizePath
   * @dataProvider normalizePathProvider
   */
  public function testNormalizePath($human_name, $expected) {
    $this->assertEquals($expected, Utils::normalizePath($human_name));
  }

  /**
   * Data provider callback for testNormalizePath().
   */
  public function normalizePathProvider() {
    return [
      ['/var/www/test', '/var/www/test'],
      ['./test', 'test'],
      ['../test', '../test'],
      ['../test/abc/../foo', '../test/foo'],
      ['..\test/abc\../../bar/../foo', '../foo'],
    ];
  }

  /**
   * Test callback.
   *
   * @covers Utils::defaultQuestions
   */
  public function testDefaultQuestions() {
    $expected = [
      'name' => ['Module name'],
      'machine_name' => [
        'Module machine name',
        NULL,
        ['DrupalCodeGenerator\Utils', 'validateMachineName'],
      ],
    ];
    $this->assertEquals($expected, Utils::defaultQuestions());
  }

  /**
   * Test callback.
   *
   * @covers Utils::getExtensionRoot
   */
  public function testGetExtensionRoot() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

}
