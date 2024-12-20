<?php

namespace ValueParsers\Test;

use Comparable;
use DataValues\DataValue;
use PHPUnit\Framework\TestCase;
use ValueParsers\ParserOptions;
use ValueParsers\ValueParser;

/**
 * Base for unit tests for ValueParser implementing classes.
 *
 * @since 0.1
 *
 * @group ValueParsers
 * @group DataValueExtensions
 *
 * @license GPL-2.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class ValueParserTestCase extends TestCase {

	protected const NON_VALID_CASES = [
		array( true ),
		array( false ),
		array( null ),
		array( 4.2 ),
		array( array() ),
		array( 42 )
	];

	/**
	 * @since 0.1
	 *
	 * @return array[]
	 */
	abstract public function validInputProvider();

	/**
	 * @since 0.1
	 *
	 * @return array[]
	 */
	abstract public function invalidInputProvider();

	/**
	 * @since 0.1
	 *
	 * @return ValueParser
	 */
	abstract protected function getInstance();

	/**
	 * @since 0.1
	 *
	 * @dataProvider validInputProvider
	 * @param mixed $value
	 * @param mixed $expected
	 * @param ValueParser|null $parser
	 */
	public function testParseWithValidInputs( $value, $expected, ?ValueParser $parser = null ) {
		if ( $parser === null ) {
			$parser = $this->getInstance();
		}

		$this->assertSmartEquals( $expected, $parser->parse( $value ) );
	}

	/**
	 * @param DataValue|mixed $expected
	 * @param DataValue|mixed $actual
	 */
	private function assertSmartEquals( $expected, $actual ) {
		if ( $this->requireDataValue() || $expected instanceof Comparable ) {
			if ( $expected instanceof DataValue && $actual instanceof DataValue ) {
				$msg = "testing equals():\n"
					. preg_replace( '/\s+/', ' ', print_r( $actual->toArray(), true ) ) . " should equal\n"
					. preg_replace( '/\s+/', ' ', print_r( $expected->toArray(), true ) );
			} else {
				$msg = 'testing equals()';
			}

			$this->assertTrue( $expected->equals( $actual ), $msg );
		} else {
			$this->assertEquals( $expected, $actual );
		}
	}

	/**
	 * @since 0.1
	 *
	 * @dataProvider invalidInputProvider
	 * @param mixed $value
	 * @param ValueParser|null $parser
	 */
	public function testParseWithInvalidInputs( $value, ?ValueParser $parser = null ) {
		if ( $parser === null ) {
			$parser = $this->getInstance();
		}

		$this->expectException( 'ValueParsers\ParseException' );
		$parser->parse( $value );
	}

	/**
	 * Returns if the result of the parsing process should be checked to be a DataValue.
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	protected function requireDataValue() {
		return true;
	}

	public function testSetAndGetOptions() {
		$parser = $this->getInstance();

		$parser->setOptions( new ParserOptions() );

		$this->assertEquals( new ParserOptions(), $parser->getOptions() );

		$options = new ParserOptions();
		$options->setOption( '~=[,,_,,]:3', '~=[,,_,,]:3' );

		$parser->setOptions( $options );

		$this->assertEquals( $options, $parser->getOptions() );
	}
}
