<?php

namespace DataValues;

/**
 * Class representing a time value.
 * @see https://www.mediawiki.org/wiki/Wikibase/DataModel#Dates_and_times
 *
 * @since 0.1
 *
 * @license GPL-2.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Kreuz
 */
class TimeValue extends DataValueObject {

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR1G instead
	 */
	public const PRECISION_Ga = self::PRECISION_YEAR1G;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR100M instead
	 */
	public const PRECISION_100Ma = self::PRECISION_YEAR100M;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR10M instead
	 */
	public const PRECISION_10Ma = self::PRECISION_YEAR10M;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR1M instead
	 */
	public const PRECISION_Ma = self::PRECISION_YEAR1M;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR100K instead
	 */
	public const PRECISION_100ka = self::PRECISION_YEAR100K;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR10K instead
	 */
	public const PRECISION_10ka = self::PRECISION_YEAR10K;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR1K instead
	 */
	public const PRECISION_ka = self::PRECISION_YEAR1K;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR100 instead
	 */
	public const PRECISION_100a = self::PRECISION_YEAR100;

	/**
	 * @deprecated since 0.8, use PRECISION_YEAR10 instead
	 */
	public const PRECISION_10a = self::PRECISION_YEAR10;

	/**
	 * @since 0.8
	 */
	public const PRECISION_YEAR1G = 0;

	/**
	 * @since 0.8
	 */
	public const PRECISION_YEAR100M = 1;

	/**
	 * @since 0.8
	 */
	public const PRECISION_YEAR10M = 2;

	/**
	 * @since 0.8
	 */
	public const PRECISION_YEAR1M = 3;

	/**
	 * @since 0.8
	 */
	public const PRECISION_YEAR100K = 4;

	/**
	 * @since 0.8
	 */
	public const PRECISION_YEAR10K = 5;

	/**
	 * @since 0.8
	 */
	public const PRECISION_YEAR1K = 6;

	/**
	 * @since 0.8
	 */
	public const PRECISION_YEAR100 = 7;

	/**
	 * @since 0.8
	 */
	public const PRECISION_YEAR10 = 8;

	public const PRECISION_YEAR = 9;
	public const PRECISION_MONTH = 10;
	public const PRECISION_DAY = 11;
	public const PRECISION_HOUR = 12;
	public const PRECISION_MINUTE = 13;
	public const PRECISION_SECOND = 14;

	/**
	 * @since 0.7.1
	 */
	public const CALENDAR_GREGORIAN = 'http://www.wikidata.org/entity/Q1985727';

	/**
	 * @since 0.7.1
	 */
	public const CALENDAR_JULIAN = 'http://www.wikidata.org/entity/Q1985786';

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
	 * Unit used for the getBefore() and getAfter() values. Use one of the self::PRECISION_...
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
	 * @param string $timestamp Timestamp in a format resembling ISO 8601.
	 * @param int $timezone Time zone offset from UTC in minutes.
	 * @param int $before Number of units given by the precision.
	 * @param int $after Number of units given by the precision.
	 * @param int $precision One of the self::PRECISION_... constants.
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
		} elseif ( $hour > 23 ) {
			throw new IllegalValueException( 'Hour out of allowed bounds' );
		} elseif ( $minute > 59 ) {
			throw new IllegalValueException( 'Minute out of allowed bounds' );
		} elseif ( $second > 61 ) {
			throw new IllegalValueException( 'Second out of allowed bounds' );
		}

		if ( $month < 1 && $day > 0 ) {
			throw new IllegalValueException( 'Cannot have a day with no month' );
		}

		if ( $day < 1 && ( $hour > 0 || $minute > 0 || $second > 0 ) ) {
			throw new IllegalValueException( 'Cannot have hour, minute or second with no day' );
		}

		// Warning, never cast the year to integer to not run into 32-bit integer overflows!
		$year = ltrim( $year, '0' );
		$year = str_pad( $year, 4, '0', STR_PAD_LEFT );

		return $sign . $year . '-' . $month . '-' . $day . 'T' . $hour . ':' . $minute . ':' . $second . 'Z';
	}

	/**
	 * @see $timestamp
	 *
	 * @return string
	 */
	public function getTime() {
		return $this->timestamp;
	}

	/**
	 * @see $calendarModel
	 *
	 * @return string URI
	 */
	public function getCalendarModel() {
		return $this->calendarModel;
	}

	/**
	 * @see $before
	 *
	 * @return int Amount
	 */
	public function getBefore() {
		return $this->before;
	}

	/**
	 * @see $after
	 *
	 * @return int Amount
	 */
	public function getAfter() {
		return $this->after;
	}

	/**
	 * @see $precision
	 *
	 * @return int one of the self::PRECISION_... constants
	 */
	public function getPrecision() {
		return $this->precision;
	}

	/**
	 * @see $timezone
	 *
	 * @return int Minutes
	 */
	public function getTimezone() {
		return $this->timezone;
	}

	/**
	 * @see DataValue::getType
	 *
	 * @return string
	 */
	public static function getType() {
		return 'time';
	}

	/**
	 * @see DataValue::getSortKey
	 *
	 * @return string
	 */
	public function getSortKey() {
		return $this->timestamp;
	}

	/**
	 * @see DataValue::getValue
	 *
	 * @return self
	 */
	public function getValue() {
		return $this;
	}

	/**
	 * @see Serializable::serialize
	 *
	 * @return string
	 */
	public function serialize() {
		return json_encode( $this->__serialize() );
	}

	public function __serialize(): array {
		return array_values( $this->getArrayValue() );
	}

	/**
	 * @see Serializable::unserialize
	 *
	 * @param string $value
	 *
	 * @throws IllegalValueException
	 */
	public function unserialize( $value ) {
		$this->__unserialize( json_decode( $value ) );
	}

	public function __unserialize( array $data ): void {
		list( $timestamp, $timezone, $before, $after, $precision, $calendarModel ) = $data;
		$this->__construct( $timestamp, $timezone, $before, $after, $precision, $calendarModel );
	}

	/**
	 * @see DataValue::getArrayValue
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
	 * Constructs a new instance from the provided data. Required for @see DataValueDeserializer.
	 * This is expected to round-trip with @see getArrayValue.
	 *
	 * @deprecated since 0.8.6. Static DataValue::newFromArray constructors like this are
	 *  underspecified (not in the DataValue interface), and misleadingly named (should be named
	 *  newFromArrayValue). Instead, use DataValue builder callbacks in @see DataValueDeserializer.
	 *
	 * @param mixed $data Warning! Even if this is expected to be a value as returned by
	 *  @see getArrayValue, callers of this specific newFromArray implementation cannot guarantee
	 *  this. This is not even guaranteed to be an array!
	 *
	 * @throws IllegalValueException if $data is not in the expected format. Subclasses of
	 *  InvalidArgumentException are expected and properly handled by @see DataValueDeserializer.
	 * @return self
	 */
	public static function newFromArray( $data ) {
		self::requireArrayFields(
			$data,
			array( 'time', 'timezone', 'before', 'after', 'precision', 'calendarmodel' )
		);

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
