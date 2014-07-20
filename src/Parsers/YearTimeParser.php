<?php

namespace DataValues\Time\Parsers;

use DataValues\Time\Values\TimeValue;
use ValueParsers\ParseException;
use ValueParsers\ParserOptions;
use ValueParsers\StringValueParser;
use ValueParsers\ValueParser;

/**
 * @since 0.7
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class YearTimeParser extends StringValueParser {

	const FORMAT_NAME = 'year';

	/**
	 * @var BaseTimeParser
	 */
	private $baseTimeParser;

	/**
	 * @var EraParser
	 */
	private $eraParser;

	/**
	 * @param ValueParser $eraParser
	 * @param ParserOptions $options
	 */
	public function __construct( ValueParser $eraParser, ParserOptions $options = null ) {
		if( is_null( $options ) ) {
			$options = new ParserOptions();
		}
		parent::__construct( $options );

		$this->baseTimeParser = new BaseTimeParser(
			new CalendarModelParser(),
			$this->getOptions()
		);

		$this->eraParser = $eraParser;
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
		list( $sign, $year ) = $this->eraParser->parse( $value );

		if( !preg_match( '/^\d+$/', $year ) ) {
			throw new ParseException( 'Failed to parse year', $value, self::FORMAT_NAME );
		}

		return $this->baseTimeParser->parse( $sign . $year . '-00-00T00:00:00Z' );
	}

}
