<?php

namespace ValueParsers\Test;

use ValueFormatters\TimeFormatter;
use ValueParsers\CalendarModelParser;

/**
 * @covers \ValueParsers\CalendarModelParser
 *
 * @group DataValue
 * @group DataValueExtensions
 *
 * @author Adam Shorland
 */
class CalendarModelParserTest extends ValueParserTestBase {

	/**
	 * @deprecated since 0.3, just use getInstance.
	 */
	protected function getParserClass() {
		return 'ValueParsers\CalendarModelParser';
	}

	/**
	 * @see ValueParserTestBase::getInstance
	 *
	 * @return CalendarModelParser
	 */
	protected function getInstance() {
		return new CalendarModelParser();
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
			array( '', TimeFormatter::CALENDAR_GREGORIAN ),
			array( ' ', TimeFormatter::CALENDAR_GREGORIAN ),
			array( 'Gregorian', TimeFormatter::CALENDAR_GREGORIAN ),
			array( 'GreGOrIAN', TimeFormatter::CALENDAR_GREGORIAN ),
			array( ' Gregorian ', TimeFormatter::CALENDAR_GREGORIAN ),
			array( TimeFormatter::CALENDAR_GREGORIAN, TimeFormatter::CALENDAR_GREGORIAN ),

			array( 'julian', TimeFormatter::CALENDAR_JULIAN ),
			array( 'JULIAN', TimeFormatter::CALENDAR_JULIAN ),
			array( ' Julian ', TimeFormatter::CALENDAR_JULIAN ),
			array( TimeFormatter::CALENDAR_JULIAN, TimeFormatter::CALENDAR_JULIAN ),
		);
	}

	/**
	 * @see ValueParserTestBase::invalidInputProvider
	 */
	public function invalidInputProvider() {
		return array(
			array( null ),
			array( true ),
			array( 1 ),
			array( 'foobar' ),
		);
	}

}
