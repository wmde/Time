<?php

namespace ValueParsers\Test;

use ValueFormatters\TimeFormatter;

/**
 * @covers \ValueParsers\CalendarModelParser
 *
 * @group DataValue
 * @group DataValueExtensions
 *
 * @author Adam Shorland
 */
class CalendarModelParserTest extends ValueParserTestBase {

	protected function getParserClass() {
		return 'ValueParsers\CalendarModelParser';
	}

	protected function requireDataValue() {
		return false;
	}

	public function validInputProvider() {
		return array(
			array( '', TimeFormatter::CALENDAR_GREGORIAN ),
			array( 'Gregorian', TimeFormatter::CALENDAR_GREGORIAN ),
			array( 'GreGOrIAN', TimeFormatter::CALENDAR_GREGORIAN ),
			array( 'julian', TimeFormatter::CALENDAR_JULIAN ),
			array( 'JULIAN', TimeFormatter::CALENDAR_JULIAN ),
		);
	}

	public function invalidInputProvider() {
		return array(
			array( 'foobar' ),
		);
	}
}
