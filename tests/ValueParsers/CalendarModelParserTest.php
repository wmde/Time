<?php

namespace ValueParsers\Test;

use ValueFormatters\TimeFormatter;
use ValueParsers\CalendarModelParser;

/**
 * @covers ValueParsers\CalendarModelParser
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
		throw new \LogicException( 'Should not be called, use getInstance' );
	}

	/**
	 * @see ValueParserTestBase::getInstance
	 *
	 * @return CalendarModelParser
	 */
	protected function getInstance() {
		return new CalendarModelParser();
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
