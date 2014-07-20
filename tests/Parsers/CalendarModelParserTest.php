<?php

namespace DataValues\Time\Parsers\Tests;

use DataValues\Time\Formatters\TimeFormatter;
use ValueParsers\Test\ValueParserTestBase;

/**
 * @covers DataValues\Time\Parsers\CalendarModelParser
 *
 * @group DataValue
 * @group DataValueExtensions
 *
 * @author Adam Shorland
 */
class CalendarModelParserTest extends ValueParserTestBase {

	protected function getParserClass() {
		return 'DataValues\Time\Parsers\CalendarModelParser';
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