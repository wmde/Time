<?php

namespace ValueParsers;

use DataValues\TimeValue;

/**
 * A straight parser that accepts various strings representing a year, and only a year. Accepts
 * years before common era as well as optional thousands separators. Should be called after
 * YearMonthTimeParser when you want to accept both formats, because strings like "1 999" may either
 * represent a month and a year or a year with digit grouping.
 *
 * @since 0.8.4
 *
 * @license GPL-2.0-or-later
 * @author Addshore
 * @author Thiemo Kreuz
 */
class YearTimeParser extends StringValueParser {

	private const FORMAT_NAME = 'year';

	/**
	 * Option to allow parsing of years with localized digit group separators. For example, the
	 * English year -10,000 (with a comma) is written as -10.000 (with a dot) in German.
	 */
	public const OPT_DIGIT_GROUP_SEPARATOR = 'digitGroupSeparator';

	/**
	 * Default, canonical digit group separator, as in the year -10,000.
	 */
	public const CANONICAL_DIGIT_GROUP_SEPARATOR = ',';

	/**
	 * @var ValueParser
	 */
	private $eraParser;

	/**
	 * @var ValueParser
	 */
	private $isoTimestampParser;

	/**
	 * @see StringValueParser::__construct
	 *
	 * @param ValueParser|null $eraParser
	 * @param ParserOptions|null $options
	 */
	public function __construct( ValueParser $eraParser = null, ParserOptions $options = null ) {
		parent::__construct( $options );

		$this->defaultOption(
			self::OPT_DIGIT_GROUP_SEPARATOR,
			self::CANONICAL_DIGIT_GROUP_SEPARATOR
		);

		$this->eraParser = $eraParser ?: new EraParser( $this->options );
		$this->isoTimestampParser = new IsoTimestampParser( null, $this->options );
	}

	/**
	 * @see StringValueParser::stringParse
	 *
	 * @param string $value
	 *
	 * @throws ParseException
	 * @return TimeValue
	 */
	protected function stringParse( $value ) {
		list( $sign, $year ) = $this->eraParser->parse( $value );
		$year = trim( $year );

		// Negative dates usually don't have a month, assume non-digits are thousands separators
		if ( $sign === '-' ) {
			$separator = $this->getOption( self::OPT_DIGIT_GROUP_SEPARATOR );
			// Always accept ISO (e.g. "1 000 BC") as well as programming style (e.g. "-1_000")
			$year = preg_replace(
				'/(?<=\d)[' . preg_quote( $separator, '/' ) . '\s_](?=\d)/',
				'',
				$year
			);
		}

		if ( !preg_match( '/^\d+\z/', $year ) ) {
			throw new ParseException( 'Failed to parse year', $value, self::FORMAT_NAME );
		}

		return $this->isoTimestampParser->parse( $sign . $year . '-00-00T00:00:00Z' );
	}

}
