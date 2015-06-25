<?php

namespace ValueParsers\Test;

use ValueParsers\CalendarModelParser;
use ValueParsers\ParserOptions;

/**
 * @covers ValueParsers\CalendarModelParser
 *
 * @group DataValue
 * @group DataValueExtensions
 *
 * @author Adam Shorland
 * @author Thiemo Mättig
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
		$options = new ParserOptions();

		$options->setOption( CalendarModelParser::OPT_CALENDAR_MODEL_URIS, array(
			'Localized' => 'Unlocalized',
		) );

		return new CalendarModelParser( $options );
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
		$gregorian = 'http://www.wikidata.org/entity/Q1985727';
		$julian = 'http://www.wikidata.org/entity/Q1985786';

		return array(
			array( '', $gregorian ),
			array( 'Gregorian', $gregorian ),
			array( 'Julian', $julian ),

			// White space
			array( ' ', $gregorian ),
			array( ' Gregorian ', $gregorian ),
			array( ' Julian ', $julian ),

			// Capitalization
			array( 'GreGOrIAN', $gregorian ),
			array( 'julian', $julian ),
			array( 'JULIAN', $julian ),

			// See https://en.wikipedia.org/wiki/Gregorian_calendar
			array( 'Western', $gregorian ),
			array( 'Christian', $gregorian ),

			// URIs
			array( 'http://www.wikidata.org/entity/Q1985727', $gregorian ),
			array( 'http://www.wikidata.org/entity/Q1985786', $julian ),

			// Via OPT_CALENDAR_MODEL_URIS
			array( 'Localized', 'Unlocalized' ),
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

			// Do not confuse Greece with Gregorian
			array( 'gr' ),
			array( 'gre' ),

			// Do not confuse July with Julian
			array( 'Jul' ),
			array( 'J' ),

			// Strict comparison for URIs and strings given via OPT_CALENDAR_MODEL_URIS
			array( 'http://www.wikidata.org/entity/Q1985727 ' ),
			array( 'Localized ' ),
			array( 'localized' ),
			array( 'LOCALIZED' ),
		);
	}

}
