<?php

namespace ValueParsers;

use DataValues\IllegalValueException;
use DataValues\TimeValue;
use InvalidArgumentException;

/**
 * ValueParser that parses the string representation of a time.
 *
 * @since 0.2
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class TimeParser extends StringValueParser {

	/**
	 * Regex pattern constant matching the sign preceding the time
	 */
	const SIGN_PATTERN = '([\+\-]?)';
	/**
	 * Regex pattern constant matching the ISO timestamp
	 */
	const TIME_PATTERN = '(\d{1,16}\-\d{2}\-\d{2}T\d{2}:\d{2}:\d{2}Z)';

	/**
	 * @var CalenderModelParser
	 */
	private $calenderModelParser;

	/**
	 * @since 0.1
	 *
	 * @param CalenderModelParser $calenderModelParser
	 * @param ParserOptions|null $options
	 */
	public function __construct( CalenderModelParser $calenderModelParser, ParserOptions $options = null ) {
		parent::__construct( $options );
		$this->calenderModelParser = $calenderModelParser;
	}

	protected function stringParse( $value ) {
		list( $sign, $time, $model ) = $this->splitTimeString( $value );
		$time = $this->padTime( $time );
		$precision = $this->getPrecisionFromTime( $sign . $time );

		try {
			$time = $this->newTimeFromParts( $sign . $time, $model, $precision );
			return $time;
		} catch ( IllegalValueException $ex ) {
			throw new ParseException( $ex->getMessage() );
		}
	}

	/**
	 * Pads the given timestamp to force year to have 16 digits
	 * @param string $time in a format such as 0002013-07-16T01:02:03Z
	 * @return string
	 */
	private function padTime( $time ) {
		return str_pad( $time, 32, '0', STR_PAD_LEFT );
	}

	private function getPrecisionFromTime( $time ) {
		/**
		 * $matches for +0000000000002013-07-16T01:02:03Z
		 * [0] => +0000000000002013-07-16T00:00:00Z
		 * [1] => +
		 * [2] => 0000000000002013
		 * [5] => 07
		 * [6] => 16
		 * [7] => 01
		 * [8] => 02
		 * [9] => 03
		 */
		preg_match( '/^(\+|\-)(\d{1,16})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})Z/',
			$time, $matches );
		list( , , $years, $months, $days, $hours, $mins, $secs ) = $matches;
		if( $secs !== '00' ) {
			return TimeValue::PRECISION_SECOND;
		}
		if( $mins !== '00' ) {
			return TimeValue::PRECISION_MINUTE;
		}
		if( $hours !== '00' ) {
			return TimeValue::PRECISION_HOUR;
		}
		if( $days !== '00' ) {
			return TimeValue::PRECISION_DAY;
		}
		if( $months !== '00' ) {
			return TimeValue::PRECISION_MONTH;
		}
		return $this->getPrecisionFromYear( $years );
	}

	/**
	 * @param string $year
	 * @return int precision
	 */
	private function getPrecisionFromYear( $year ) {
		$rightZeros = strlen( $year ) - strlen( rtrim( $year, '0' ) );
		$precision = TimeValue::PRECISION_YEAR - $rightZeros;
		if( $precision < TimeValue::PRECISION_Ga ) {
			$precision = TimeValue::PRECISION_Ga;
		}
		return $precision;
	}

	private function newTimeFromParts( $time, $model, $precision ) {
		$model = $this->calenderModelParser->parse( $model );
		return new TimeValue( $time, 0, 0, 0, $precision, $model );
	}

	private function splitTimeString( $value ) {
		if ( !is_string( $value ) ) {
			throw new InvalidArgumentException( '$value must be a string' );
		}

		$pattern = '@^'
			. '\s*' . self::SIGN_PATTERN . '' // $1: sign
			. '\s*' . self::TIME_PATTERN . '' // $2: time
			. '\s*\(?\s*' . CalenderModelParser::MODEL_PATTERN . '\s*\)?' // $3 model
			. '\s*$@iu';

		if ( !preg_match( $pattern, $value, $groups ) ) {
			throw new ParseException( 'Malformed time: ' . $value );
		}

		array_shift( $groups ); // remove $groups[0]
		return $groups;
	}

}