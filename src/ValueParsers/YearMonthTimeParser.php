<?php

namespace ValueParsers;

use DataValues\TimeValue;

/**
 * A parser that accepts various date formats with month precision. Prefers month/year order when
 * both numbers are valid months, e.g. "12/10" is December 2010. Should be called before
 * YearTimeParser when you want to accept both formats, because strings like "1 999" may either
 * represent a month and a year or a year with digit grouping.
 *
 * @since 0.8.4
 *
 * @license GPL-2.0+
 * @author Addshore
 * @author Thiemo Kreuz
 *
 * @todo match BCE dates in here
 */
class YearMonthTimeParser extends StringValueParser {

	const FORMAT_NAME = 'year-month';

	/**
	 * @var int[] Array mapping localized month names to month numbers (1 to 12).
	 */
	private $monthNumbers;

	/**
	 * @var ValueParser
	 */
	private $isoTimestampParser;

	/**
	 * @see StringValueParser::__construct
	 *
	 * @param MonthNameProvider $monthNameProvider
	 * @param ParserOptions|null $options
	 */
	public function __construct(
		MonthNameProvider $monthNameProvider,
		ParserOptions $options = null
	) {
		parent::__construct( $options );

		$languageCode = $this->getOption( ValueParser::OPT_LANG );
		$this->monthNumbers = $monthNameProvider->getMonthNumbers( $languageCode );
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
		// Matches year and month separated by a separator.
		// \p{L} matches letters outside the ASCII range.
		$regex = '/^(-?[\d\p{L}]+)\s*?[\/\-\s.,]\s*(-?[\d\p{L}]+)$/u';
		if ( !preg_match( $regex, trim( $value ), $matches ) ) {
			throw new ParseException( 'Failed to parse year and month', $value, self::FORMAT_NAME );
		}
		list( , $a, $b ) = $matches;

		$aIsInt = preg_match( '/^-?\d+$/', $a );
		$bIsInt = preg_match( '/^-?\d+$/', $b );

		if ( $aIsInt && $bIsInt ) {
			if ( $this->canBeMonth( $a ) ) {
				return $this->getTimeFromYearMonth( $b, $a );
			} elseif ( $this->canBeMonth( $b ) ) {
				return $this->getTimeFromYearMonth( $a, $b );
			}
		} elseif ( $aIsInt ) {
			$month = $this->parseMonth( $b );

			if ( $month ) {
				return $this->getTimeFromYearMonth( $a, $month );
			}
		} elseif ( $bIsInt ) {
			$month = $this->parseMonth( $a );

			if ( $month ) {
				return $this->getTimeFromYearMonth( $b, $month );
			}
		}

		throw new ParseException( 'Failed to parse year and month', $value, self::FORMAT_NAME );
	}

	/**
	 * @param string $month
	 *
	 * @return int|null
	 */
	private function parseMonth( $month ) {
		foreach ( $this->monthNumbers as $monthName => $i ) {
			if ( strcasecmp( $monthName, $month ) === 0 ) {
				return $i;
			}
		}

		return null;
	}

	/**
	 * @param string $year
	 * @param string $month as a canonical month number
	 *
	 * @return TimeValue
	 */
	private function getTimeFromYearMonth( $year, $month ) {
		if ( $year[0] !== '-' ) {
			$year = '+' . $year;
		}

		return $this->isoTimestampParser->parse( sprintf( '%s-%02s-00T00:00:00Z', $year, $month ) );
	}

	/**
	 * @param string $value
	 *
	 * @return bool can the given value be a month?
	 */
	private function canBeMonth( $value ) {
		return $value >= 0 && $value <= 12;
	}

}
