<?php

namespace ValueParsers\Test;

use ValueParsers\EraParser;
use PHPUnit\Framework\TestCase;
use ValueParsers\ParserOptions;
use ValueParsers\ValueParser;

/**
 * @covers ValueParsers\EraParser
 *
 * @group DataValue
 * @group DataValueExtensions
 * @group TimeParsers
 * @group ValueParsers
 *
 * @license GPL-2.0-or-later
 * @author Addshore
 * @author Thiemo Kreuz
 */
class EraParserTest extends TestCase {

	/**
	 * @see ValueParserTestBase::getInstance
	 *
	 * @return EraParser
	 */
	protected function getInstance() {
		return new EraParser();
	}

	/**
	 * @see ValueParserTestBase::requireDataValue
	 *
	 * @return bool
	 */
	protected function requireDataValue() {
		return false;
	}

	/**
	 * @see ValueParserTestBase::validInputProvider
	 */
	public function validInputProvider() {
		return array(
			// Strings with no explicit era should be echoed
			array( '1', array( '+', '1' ) ),
			array( '1 000 000', array( '+', '1 000 000' ) ),
			array( 'non-matching string', array( '+', 'non-matching string' ) ),

			// Strings with an era that should be split of
			array( '+100', array( '+', '100' ) ),
			array( '-100', array( '-', '100' ) ),
			array( '   -100', array( '-', '100' ) ),
			array( '100BC', array( '-', '100' ) ),
			array( '100 BC', array( '-', '100' ) ),
			array( '100 BCE', array( '-', '100' ) ),
			array( '100 AD', array( '+', '100' ) ),
			array( '100 A. D.', array( '+', '100' ) ),
			array( '   100   B.   C.   ', array( '-', '100' ) ),
			array( '   100   Common   Era   ', array( '+', '100' ) ),
			array( '100 CE', array( '+', '100' ) ),
			array( '100CE', array( '+', '100' ) ),
			array( '+100', array( '+', '100' ) ),
			array( '100 Common Era', array( '+', '100' ) ),
			array( '100 Current Era', array( '+', '100' ) ),
			array( '100 Christian Era', array( '+', '100' ) ),
			array( '100Common Era', array( '+', '100' ) ),
			array( '100 Before Common Era', array( '-', '100' ) ),
			array( '100 Before Current Era', array( '-', '100' ) ),
			array( '100 Before Christian Era', array( '-', '100' ) ),
			array( '1 July 2013 Before Common Era', array( '-', '1 July 2013' ) ),
			array( 'June 2013 Before Common Era', array( '-', 'June 2013' ) ),
			array( '10-10-10 Before Common Era', array( '-', '10-10-10' ) ),
			array( 'FooBefore Common Era', array( '-', 'Foo' ) ),
			array( 'Foo Before Common Era', array( '-', 'Foo' ) ),
			array( '-1 000 000', array( '-', '1 000 000' ) ),
			array( '1 000 000 B.C.', array( '-', '1 000 000' ) ),
		);
	}

	/**
	 * @see StringValueParserTest::invalidInputProvider
	 */
	public function invalidInputProvider() {
		return array(
			// Reject strings with two eras, no matter if conflicting or not
			array( '-100BC' ),
			array( '-100AD' ),
			array( '-100CE' ),
			array( '+100BC' ),
			array( '+100AD' ),
			array( '+100CE' ),
			array( '+100 Before Common Era' ),
			array( '+100 Common Era' ),
		);
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

	/**
	 * @since 0.1
	 *
	 * @dataProvider validInputProvider
	 * @param mixed $value
	 * @param mixed $expected
	 * @param ValueParser|null $parser
	 */
	public function testParseWithValidInputs( $value, $expected, ValueParser $parser = null ) {
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
		}
		else {
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
	public function testParseWithInvalidInputs( $value, ValueParser $parser = null ) {
		if ( $parser === null ) {
			$parser = $this->getInstance();
		}

		$this->expectException( 'ValueParsers\ParseException' );
		$parser->parse( $value );
	}
}
