<?php

namespace ValueParsers;

use DataValues\TimeValue;
use InvalidArgumentException;

/**
 * @since 0.7
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 *
 * @todo match BCE dates in here
 */
class YearMonthTimeParser extends StringValueParser {

	const FORMAT_NAME = 'yearmonth';

	/**
	 * @var array[]
	 */
	private $monthNameMaps;

	/**
	 * @param array[] $monthNameMaps array of lists of month names each with keys 1 to 12 representing months
	 *                these lists will be processes in order, of list keys.
	 *                I.e. All month names in the first array will be checked first so full names should come first
	 *                     and abbreviations should come last
	 * @param ParserOptions $options
	 */
	public function __construct( array $monthNameMaps, ParserOptions $options = null ) {
		$this->throwExceptionsOnBadMonthNameMaps( $monthNameMaps );
		$this->monthNameMaps = $monthNameMaps;
		parent::__construct( $options );
	}

	/**
	 * @param array[] $monthNameMaps
	 *
	 * @throws InvalidArgumentException
	 */
	private function throwExceptionsOnBadMonthNameMaps( $monthNameMaps ) {
		foreach( $monthNameMaps as $map ) {
			if( !is_array( $map ) ) {
				throw new InvalidArgumentException( '$monthNameMaps must be an array of arrays' );
			}
			foreach( $map as $key => $mapElement ) {
				if( $key > 12 || $key < 1 ) {
					throw new InvalidArgumentException( 'Each month name map must have keys between 1 and 12, got ' . strval( $key ) . ' as a key' );
				}
				if( !is_string( $mapElement ) ) {
					throw new InvalidArgumentException( 'Each month name map must have string elements' );
				}
			}
		}
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
		//Matches Year and month separated by a separator, \p{L} matches letters outside the ASCII range
		if( !preg_match( '/^([\d\p{L}]+)\s*[\/\-\s.,]\s*([\d\p{L}]+)$/', trim( $value ), $matches ) ) {
			throw new ParseException( 'Failed to parse year and month', $value, self::FORMAT_NAME );
		}
		list( , $a, $b ) = $matches;

		$aIsInt = preg_match( '/^\d+$/', $a );
		$bIsInt = preg_match( '/^\d+$/', $b );

		if( $aIsInt && $bIsInt ) {
			$parsed = $this->parseYearMonthTwoInts( $a, $b );
			if( $parsed ) {
				return $parsed;
			}
		}

		if( $aIsInt || $bIsInt ) {
			if( $aIsInt ) {
				$year = $a;
				$month = trim( $b );
			} else {
				$year = $b;
				$month = trim( $a );
			}

			$parsed =  $this->parseYearMonth( $year, $month );
			if( $parsed ) {
				return $parsed;
			}
		}

		throw new ParseException( 'Failed to parse year and month', $value, self::FORMAT_NAME );
	}

	/**
	 * If we have 2 integers parse the date assuming that the larger is the year
	 * unless the smaller is not a 'legal' month value
	 *
	 * @param string|int $a
	 * @param string|int $b
	 *
	 * @return TimeValue|bool
	 */
	private function parseYearMonthTwoInts( $a, $b  ) {
		if( !preg_match( '/^\d+$/', $a ) || !preg_match( '/^\d+$/', $b ) ) {
			return false;
		}

		if( !$this->canBeMonth( $a ) && $this->canBeMonth( $b ) ) {
			return $this->getTimeFromYearMonth( $a, $b );
		} elseif( $this->canBeMonth( $a ) ) {
			return $this->getTimeFromYearMonth( $b, $a );
		}

		return false;
	}

	/**
	 * If we have 1 int and 1 string then try to parse the int as the year and month as the string
	 * Check for both the full name and abbreviations
	 *
	 * @param string|int $year
	 * @param string|int $month
	 *
	 * @return TimeValue|bool
	 */
	private function parseYearMonth( $year, $month ) {
		foreach( $this->monthNameMaps as $map ) {
			foreach( $map as $monthInt => $monthName ) {
				if( strcasecmp( $monthName, $month ) === 0 ) {
					return $this->getTimeFromYearMonth( $year, $monthInt );
				}
			}
		}

		return false;
	}

	/**
	 * @param string $year
	 * @param string $month
	 * @return TimeValue
	 */
	private function getTimeFromYearMonth( $year, $month ) {
		$timeParser = new BaseTimeParser( new CalendarModelParser(), $this->getOptions() );
		return $timeParser->parse( sprintf( '+%d-%02d-00T00:00:00Z', $year, $month ) );
	}

	/**
	 * @param string|int $value
	 * @return bool can the given value be a month?
	 */
	private function canBeMonth( $value ) {
		$value = intval( $value );
		return $value >= 0 && $value <= 12;
	}

}