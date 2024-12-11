<?php

namespace ValueParsers\Test;

use DataValues\TimeValue;
use ValueParsers\EraParser;
use ValueParsers\ParseException;
use ValueParsers\ParserOptions;
use ValueParsers\YearTimeParser;

/**
 * @covers ValueParsers\YearTimeParser
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
class YearTimeParserTest extends ValueParserTestCase {

	/**
	 * @see ValueParserTestBase::getInstance
	 *
	 * @return YearTimeParser
	 */
	protected function getInstance() {
		return new YearTimeParser( $this->getMockEraParser() );
	}

	/**
	 * @return EraParser
	 */
	private function getMockEraParser() {
		$mock = $this->getMockBuilder( EraParser::class )
			->disableOriginalConstructor()
			->getMock();
		$mock->expects( $this->any() )
			->method( 'parse' )
			->with( $this->isType( 'string' ) )
			->willReturnCallback(
				static function ( $value ) {
					$sign = '+';
					// Tiny parser that supports a single negative sign only
					if ( $value[0] === '-' ) {
						$sign = '-';
						$value = substr( $value, 1 );
					}
					return array( $sign, $value );
				}
			);
		return $mock;
	}

	/**
	 * @see ValueParserTestBase::validInputProvider
	 */
	public function validInputProvider() {
		$gregorian = 'http://www.wikidata.org/entity/Q1985727';
		$julian = 'http://www.wikidata.org/entity/Q1985786';

		$argLists = array();

		$valid = array(
			// Whitespace
			"1999\n" =>
				array( '+1999-00-00T00:00:00Z' ),
			' 2000 ' =>
				array( '+2000-00-00T00:00:00Z' ),

			'2010' =>
				array( '+2010-00-00T00:00:00Z' ),
			'2000000' =>
				array( '+2000000-00-00T00:00:00Z', TimeValue::PRECISION_YEAR1M ),
			'2000000000' =>
				array( '+2000000000-00-00T00:00:00Z', TimeValue::PRECISION_YEAR1G ),
			'2000020000' =>
				array( '+2000020000-00-00T00:00:00Z', TimeValue::PRECISION_YEAR10K ),
			'2000001' =>
				array( '+2000001-00-00T00:00:00Z' ),
			'02000001' =>
				array( '+2000001-00-00T00:00:00Z' ),
			'1' =>
				array( '+0001-00-00T00:00:00Z', TimeValue::PRECISION_YEAR, $julian ),
			'000000001' =>
				array( '+0001-00-00T00:00:00Z', TimeValue::PRECISION_YEAR, $julian ),
			'-1000000' =>
				array( '-1000000-00-00T00:00:00Z', TimeValue::PRECISION_YEAR1M, $julian ),
			'-1 000 000' =>
				array( '-1000000-00-00T00:00:00Z', TimeValue::PRECISION_YEAR1M, $julian ),
			'-19_000' =>
				array( '-19000-00-00T00:00:00Z', TimeValue::PRECISION_YEAR1K, $julian ),
			// Digit grouping in the Indian numbering system
			'-1,99,999' =>
				array( '-199999-00-00T00:00:00Z', TimeValue::PRECISION_YEAR, $julian ),
		);

		foreach ( $valid as $value => $expected ) {
			$timestamp = $expected[0];
			$precision = isset( $expected[1] ) ? $expected[1] : TimeValue::PRECISION_YEAR;
			$calendarModel = isset( $expected[2] ) ? $expected[2] : $gregorian;

			$argLists[] = array(
				(string)$value,
				new TimeValue( $timestamp, 0, 0, 0, $precision, $calendarModel )
			);
		}

		return $argLists;
	}

	public function testDigitGroupSeparatorOption() {
		$options = new ParserOptions();
		$options->setOption( YearTimeParser::OPT_DIGIT_GROUP_SEPARATOR, '.' );
		$parser = new YearTimeParser( null, $options );
		$timeValue = $parser->parse( '-19.000' );
		$this->assertSame( '-19000-00-00T00:00:00Z', $timeValue->getTime() );
	}

	/**
	 * @see StringValueParserTest::invalidInputProvider
	 */
	public function invalidInputProvider() {
		$argLists = parent::NON_VALID_CASES;

		$invalid = array(
			// These are just wrong
			'June June June',
			'111 111 111',
			'Jann 2014',

			// Not within the scope of this parser
			'1 July 20000',

			// We should not try to parse these, this just gets confusing
			'-100BC',
			'+100BC',
			'-100 BC',
			'+100 BC',
			'+100 BCE',
			'+100BCE',

			// Non-default and invalid thousands separators
			'-,999',
			'-999,',
			'-19.000',
			'-1/000/000',

			// Positive years are unlikely to have thousands separators, it's more likely a date
			'1 000 000',
			'19_000',
			'1,99,999',
		);

		foreach ( $invalid as $value ) {
			$argLists[] = array( $value );
		}

		return $argLists;
	}

	public function testParseExceptionMessage() {
		$parser = $this->getInstance();
		$this->expectException( ParseException::class );
		$parser->parse( 'ju5t 1nval1d' );
	}

}
