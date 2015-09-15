<?php

namespace DataValues;

/**
 * Class representing a time value.
 * @see https://www.mediawiki.org/wiki/Wikibase/DataModel#Dates_and_times
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class TimeValue extends DataValueObject {

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR1G instead
	 */
	const PRECISION_Ga = TimeValue::PRECISION_YEAR1G;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR100M instead
	 */
	const PRECISION_100Ma = TimeValue::PRECISION_YEAR100M;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR10M instead
	 */
	const PRECISION_10Ma = TimeValue::PRECISION_YEAR10M;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR1M instead
	 */
	const PRECISION_Ma = TimeValue::PRECISION_YEAR1M;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR100K instead
	 */
	const PRECISION_100ka = TimeValue::PRECISION_YEAR100K;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR10K instead
	 */
	const PRECISION_10ka = TimeValue::PRECISION_YEAR10K;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR1K instead
	 */
	const PRECISION_ka = TimeValue::PRECISION_YEAR1K;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR100 instead
	 */
	const PRECISION_100a = TimeValue::PRECISION_YEAR100;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR10 instead
	 */
	const PRECISION_10a = TimeValue::PRECISION_YEAR10;

	/**
	 * @since 0.8
	 */
	const PRECISION_YEAR1G = 0;

	/**
	 * @since 0.8
	 */
	const PRECISION_YEAR100M = 1;

	/**
	 * @since 0.8
	 */
	const PRECISION_YEAR10M = 2;

	/**
	 * @since 0.8
	 */
	const PRECISION_YEAR1M = 3;

	/**
	 * @since 0.8
	 */
	const PRECISION_YEAR100K = 4;

	/**
	 * @since 0.8
	 */
	const PRECISION_YEAR10K = 5;

	/**
	 * @since 0.8
	 */
	const PRECISION_YEAR1K = 6;

	/**
	 * @since 0.8
	 */
	const PRECISION_YEAR100 = 7;

	/**
	 * @since 0.8
	 */
	const PRECISION_YEAR10 = 8;

	const PRECISION_YEAR = 9;
	const PRECISION_MONTH = 10;
	const PRECISION_DAY = 11;
	const PRECISION_HOUR = 12;
	const PRECISION_MINUTE = 13;
	const PRECISION_SECOND = 14;

	/**
	 * @since 0.7.1
	 */
	const CALENDAR_GREGORIAN = 'http://www.wikidata.org/entity/Q1985727';

	/**
	 * @since 0.7.1
	 */
	const CALENDAR_JULIAN = 'http://www.wikidata.org/entity/Q1985786';

	/**
	 * Timestamp describing a point in time. The actual format depends on the calendar model.
	 *
	 * Gregorian and Julian dates use the same YMD ordered format, resembling ISO 8601, e.g.
	 * +2013-01-01T00:00:00Z. In this format the year is always signed and padded with zero
	 * characters to have between 4 and 16 digits. Month and day can be zero, indicating they are
	 * unknown. The timezone suffix Z is meaningless and must be ignored. Use getTimezone() instead.
	 *
	 * @see $timezone
	 * @see $calendarModel
	 *
	 * @var string
	 */
	private $timestamp;

	/**
	 * Unit used for the getBefore() and getAfter() values. Use one of the TimeValue::PRECISION_...
	 * constants.
	 *
	 * @var int
	 */
	private $precision;

	/**
	 * If the date is uncertain, how many units after the given time could it be?
	 * The unit is given by the precision.
	 *
	 * @var int Amount
	 */
	private $after;

	/**
	 * If the date is uncertain, how many units before the given time could it be?
	 * The unit is given by the precision.
	 *
	 * @var int Amount
	 */
	private $before;

	/**
	 * Time zone information as an offset from UTC in minutes.
	 *
	 * @var int Minutes
	 */
	private $timezone;

	/**
	 * URI identifying the calendar model. The actual timestamp should be in this calendar model,
	 * but note that there is nothing this class can do to enforce this convention.
	 *
	 * @var string URI
	 */
	private $calendarModel;

	/**
	 * @since 0.1
	 *
	 * @param string $timestamp Timestamp in a format resembling ISO 8601.
	 * @param int $timezone Time zone offset from UTC in minutes.
	 * @param int $before Number of units given by the precision.
	 * @param int $after Number of units given by the precision.
	 * @param int $precision One of the TimeValue::PRECISION_... constants.
	 * @param string $calendarModel An URI identifying the calendar model.
	 *
	 * @throws IllegalValueException
	 */
	public function __construct( $timestamp, $timezone, $before, $after, $precision, $calendarModel ) {
		if ( !is_string( $timestamp ) || $timestamp === '' ) {
			throw new IllegalValueException( '$timestamp must be a non-empty string' );
		}

		if ( !is_int( $timezone ) ) {
			throw new IllegalValueException( '$timezone must be an integer' );
		} elseif ( $timezone < -12 * 3600 || $timezone > 14 * 3600 ) {
			throw new IllegalValueException( '$timezone out of allowed bounds' );
		}

		if ( !is_int( $before ) || $before < 0 ) {
			throw new IllegalValueException( '$before must be an unsigned integer' );
		}

		if ( !is_int( $after ) || $after < 0 ) {
			throw new IllegalValueException( '$after must be an unsigned integer' );
		}

		if ( !is_int( $precision ) ) {
			throw new IllegalValueException( '$precision must be an integer' );
		} elseif ( $precision < self::PRECISION_YEAR1G || $precision > self::PRECISION_SECOND ) {
			throw new IllegalValueException( '$precision out of allowed bounds' );
		}

		// XXX: Enforce an IRI? Or at least a size limit?
		if ( !is_string( $calendarModel ) || $calendarModel === '' ) {
			throw new IllegalValueException( '$calendarModel must be a non-empty string' );
		}

		$this->timestamp = $this->normalizeIsoTimestamp( $timestamp );
		$this->timezone = $timezone;
		$this->before = $before;
		$this->after = $after;
		$this->precision = $precision;
		$this->calendarModel = $calendarModel;
	}

	/**
	 * @param string $timestamp
	 *
	 * @throws IllegalValueException
	 * @return string
	 */
	private function normalizeIsoTimestamp( $timestamp ) {
		if ( !preg_match(
			'/^([-+])(\d{1,16})-(\d\d)-(\d\d)T(\d\d):(\d\d):(\d\d)Z$/',
			$timestamp,
			$matches
		) ) {
			throw new IllegalValueException( '$timestamp must resemble ISO 8601, given ' . $timestamp );
		}

		list( , $sign, $year, $month, $day, $hour, $minute, $second ) = $matches;

		if ( $month > 12 ) {
			throw new IllegalValueException( 'Month out of allowed bounds' );
		} elseif ( $day > 31 ) {
			throw new IllegalValueException( 'Day out of allowed bounds' );
		} elseif ( $day > 0 && $month < 1 ) {
			throw new IllegalValueException( 'Can not have a day with no month' );
		} elseif ( $hour > 23 ) {
			throw new IllegalValueException( 'Hour out of allowed bounds' );
		} elseif ( $minute > 59 ) {
			throw new IllegalValueException( 'Minute out of allowed bounds' );
		} elseif ( $second > 61 ) {
			throw new IllegalValueException( 'Second out of allowed bounds' );
		}

		// Warning, never cast the year to integer to not run into 32-bit integer overflows!
		$year = ltrim( $year, '0' );
		$year = str_pad( $year, 4, '0', STR_PAD_LEFT );

		return $sign . $year . '-' . $month . '-' . $day . 'T' . $hour . ':' . $minute .':' . $second . 'Z';
	}

	/**
	 * @see $timestamp
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getTime() {
		return $this->timestamp;
	}

	/**
	 * @see $calendarModel
	 *
	 * @since 0.1
	 *
	 * @return string URI
	 */
	public function getCalendarModel() {
		return $this->calendarModel;
	}

	/**
	 * @see $before
	 *
	 * @since 0.1
	 *
	 * @return int Amount
	 */
	public function getBefore() {
		return $this->before;
	}

	/**
	 * @see $after
	 *
	 * @since 0.1
	 *
	 * @return int Amount
	 */
	public function getAfter() {
		return $this->after;
	}

	/**
	 * @see $precision
	 *
	 * @since 0.1
	 *
	 * @return int one of the TimeValue::PRECISION_... constants
	 */
	public function getPrecision() {
		return $this->precision;
	}

	/**
	 * @see $timezone
	 *
	 * @since 0.1
	 *
	 * @return int Minutes
	 */
	public function getTimezone() {
		return $this->timezone;
	}

	/**
	 * @see DataValue::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public static function getType() {
		return 'time';
	}

	/**
	 * @see DataValue::getSortKey
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getSortKey() {
		return $this->timestamp;
	}

	/**
	 * @see DataValue::getValue
	 *
	 * @since 0.1
	 *
	 * @return TimeValue
	 */
	public function getValue() {
		return $this;
	}

	/**
	 * @see Serializable::serialize
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function serialize() {
		return json_encode( array_values( $this->getArrayValue() ) );
	}

	/**
	 * @see Serializable::unserialize
	 *
	 * @since 0.1
	 *
	 * @param string $value
	 *
	 * @throws IllegalValueException
	 */
	public function unserialize( $value ) {
		list( $timestamp, $timezone, $before, $after, $precision, $calendarModel ) = json_decode( $value );
		$this->__construct( $timestamp, $timezone, $before, $after, $precision, $calendarModel );
	}

	/**
	 * @see DataValue::getArrayValue
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public function getArrayValue() {
		return array(
			'time' => $this->timestamp,
			'timezone' => $this->timezone,
			'before' => $this->before,
			'after' => $this->after,
			'precision' => $this->precision,
			'calendarmodel' => $this->calendarModel,
		);
	}

	/**
	 * Constructs a new instance of the DataValue from the provided data.
	 * This can round-trip with @see getArrayValue
	 *
	 * @since 0.1
	 *
	 * @param mixed $data
	 *
	 * @return TimeValue
	 * @throws IllegalValueException
	 */
	public static function newFromArray( $data ) {
		self::requireArrayFields( $data, array( 'time', 'timezone', 'before', 'after', 'precision', 'calendarmodel' ) );

		return new static(
			$data['time'],
			$data['timezone'],
			$data['before'],
			$data['after'],
			$data['precision'],
			$data['calendarmodel']
		);
	}

	public function __toString() {
		return $this->timestamp;
	}

}
