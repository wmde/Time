<?php

namespace DataValues\Time\Parsers;

use DataValues\Time\Values\TimeValue;
use ValueParsers\ParseException;
use ValueParsers\ParserOptions;
use ValueParsers\StringValueParser;

/**
 * Time Parser
 *
 * @since 0.2
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class TimeParser extends StringValueParser {

	const FORMAT_NAME = 'time';

	public function __construct( ParserOptions $options = null ) {
		if( is_null( $options ) ) {
			$options = new ParserOptions();
		}
		parent::__construct( $options );
	}

	/**
	 * Parses the provided string and returns the result.
	 *
	 * @param string $value
	 *
	 * @throws ParseException
	 * @return TimeValue
	 */
	protected function stringParse( $value ) {
		foreach ( $this->getParsers() as $parser ) {
			try {
				return $parser->parse( $value );
			}
			catch ( ParseException $parseException ) {
				continue;
			}
		}

		throw new ParseException( 'The format of the time could not be determined.', $value, self::FORMAT_NAME );
	}

	/**
	 * @return  StringValueParser[]
	 */
	private function getParsers() {
		$parsers = array();

		$calenderModelParser = new CalendarModelParser( $this->getOptions() );

		$parsers[] = new BaseTimeParser(
			$calenderModelParser,
			$this->getOptions()
		);

		return $parsers;
	}

}
