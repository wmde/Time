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
 * @license GPL-2.0-or-later
 * @author Addshore
 * @author Thiemo Kreuz
 */
class YearMonthTimeParser extends StringValueParser {

	private const FORMAT_NAME = 'year-month';

	/**
	 * @var int[] Array mapping localized month names to month numbers (1 to 12).
	 */
	private $monthNumbers;

	/**
	 * @var ValueParser
	 */
	private $isoTimestampParser;

	/**
	 * @var EraParser
	 */
	private $eraParser;

	/**
	 * @see StringValueParser::__construct
	 *
	 * @param MonthNameProvider $monthNameProvider
	 * @param ParserOptions|null $options
	 * @param EraParser|null $eraParser
	 */
	public function __construct(
		MonthNameProvider $monthNameProvider,
		ParserOptions $options = null,
		EraParser $eraParser = null
	) {
		parent::__construct( $options );

		$languageCode = $this->getOption( ValueParser::OPT_LANG );
		$this->monthNumbers = $monthNameProvider->getMonthNumbers( $languageCode );
		$this->isoTimestampParser = new IsoTimestampParser( null, $this->options );
		$this->eraParser = $eraParser ?: new EraParser();
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
		list( $newValue, $sign ) = $this->splitBySignAndEra( $value );
		list( $a, $b ) = $this->splitByYearMonth( $value, $newValue );

		// non-empty sign indicates the era (e.g. "BCE") was specified
		// don't accept a negative number as the year
		$intRegex = $sign !== '' ? '/^\d+$/' : '/^-?\d+$/';
		$aIsInt = preg_match( $intRegex, $a );
		$bIsInt = preg_match( $intRegex, $b );

		if ( $aIsInt && $bIsInt ) {
			// stuff like "1 234 BCE" can be interpreted as "1234 BCE"
			// this is for YearTimeParser, don't interfere with it
			if ( $sign !== '-' ) {
				if ( $this->canBeMonth( $a ) ) {
					return $this->getTimeFromYearMonth( $sign . $b, $a );
				} elseif ( $this->canBeMonth( $b ) ) {
					return $this->getTimeFromYearMonth( $sign . $a, $b );
				}
			}
		} elseif ( $aIsInt ) {
			$month = $this->parseMonth( $b );

			if ( $month ) {
				return $this->getTimeFromYearMonth( $sign . $a, $month );
			}
		} elseif ( $bIsInt ) {
			$month = $this->parseMonth( $a );

			if ( $month ) {
				return $this->getTimeFromYearMonth( $sign . $b, $month );
			}
		}

		throw new ParseException( 'Failed to parse year and month', $value, self::FORMAT_NAME );
	}

	/**
	 * Returns two strings which can either be the month or year (depending on input order)
	 *
	 * @param string $originalValue The value as original given (for error reporting)
	 * @param string $newValue As produced by splitBySignAndEra
	 *
	 * @return array( string $a, string $b )
	 */
	private function splitByYearMonth( $originalValue, string $newValue ): array {
		// Matches year and month separated by a separator.
		// \p{L} matches letters outside the ASCII range.
		$regex = '/^(-?[\d\p{L}]+)\s*?[\/\-\s.,]\s*(-?[\d\p{L}]+)$/u';
		if ( !preg_match( $regex, $newValue, $matches ) ) {
			throw new ParseException( 'Failed to parse year and month', $originalValue, self::FORMAT_NAME );
		}
		return array_splice( $matches, 1 );
	}

	/**
	 * @param string $value
	 *
	 * @return array( string $newValue, string $sign )
	 */
	private function splitBySignAndEra( $value ) {
		$trimmedValue = trim( $value );
		$init = substr( $trimmedValue, 0, 1 );
		// we want to handle signs at the beginning ourselves
		if ( $init === '+' || $init === '-' ) {
			$newValue = $trimmedValue;
			$sign = '';
		} else {
			list( $sign, $newValue ) = $this->eraParser->parse( $trimmedValue );
			if ( $newValue === $trimmedValue ) {
				// EraParser defaults to "+" but we need to indicate "unspecified era"
				$sign = '';
			}
		}

		return [ $newValue, $sign ];
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
		if ( $year[0] !== '-' && $year[0] !== '+' ) {
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
