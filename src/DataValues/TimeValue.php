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

	const PRECISION_Ga = 0; // Gigayear
	const PRECISION_100Ma = 1; // 100 Megayears
	const PRECISION_10Ma = 2; // 10 Megayears
	const PRECISION_Ma = 3; // Megayear
	const PRECISION_100ka = 4; // 100 Kiloyears
	const PRECISION_10ka = 5; // 10 Kiloyears
	const PRECISION_ka = 6; // Kiloyear
	const PRECISION_100a = 7; // 100 years
	const PRECISION_10a = 8; // 10 years
	const PRECISION_YEAR = 9;
	const PRECISION_MONTH = 10;
	const PRECISION_DAY = 11;
	const PRECISION_HOUR = 12;
	const PRECISION_MINUTE = 13;
	const PRECISION_SECOND = 14;

	/**
	 * Timestamp describing a point in time. The actual format depends on the calendar model.
	 *
	 * Gregorian and Julian dates use the same YMD ordered format, resembling ISO 8601, e.g.
	 * +2013-01-01T00:00:00Z. In this format the year is always signed and padded with zero
	 * characters to have between 1 and 16 digits. Month and day can be zero, indicating they are
	 * unknown. The timezone suffix Z is meaningless and must be ignored. Use $timezone instead.
	 *
	 * @see $timezone
	 * @see $calendarModel
	 *
	 * @var string
	 */
	private $time;

	/**
	 * Unit used for the $after and $before values. Use one of the TimeValue::PRECISION_...
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
	 * Timezone information as an offset from UTC in minutes.
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
	 * @param string $time timestamp in a format depending on the calendar model
	 * @param int $timezone offset from UTC in minutes
	 * @param int $before number of units given by the precision
	 * @param int $after number of units given by the precision
	 * @param int $precision one of the TimeValue::PRECISION_... constants
	 * @param string $calendarModel an URI identifying the calendar model
	 *
	 * @throws IllegalValueException
	 */
	public function __construct( $time, $timezone, $before, $after, $precision, $calendarModel ) {
		if ( !is_string( $time ) ) {
			throw new IllegalValueException( '$time needs to be a string' );
		}

		// Leap seconds are a valid concept
		if ( !preg_match( '!^[-+]\d{1,16}-(0\d|1[012])-([012]\d|3[01])T([01]\d|2[0123]):[0-5]\d:([0-5]\d|6[012])Z$!', $time ) ) {
			throw new IllegalValueException( '$time must be a YMD string resembling ISO 8601, given ' . $time );
		}

		if ( !is_int( $timezone ) ) {
			throw new IllegalValueException( '$timezone needs to be an integer' );
		}

		if ( $timezone < -12 * 3600 || $timezone > 14 * 3600 ) {
			throw new IllegalValueException( '$timezone out of allowed bounds' );
		}

		if ( !is_int( $before ) || $before < 0 ) {
			throw new IllegalValueException( '$before needs to be an unsigned integer' );
		}

		if ( !is_int( $after ) || $after < 0 ) {
			throw new IllegalValueException( '$after needs to be an unsigned integer' );
		}

		if ( !is_int( $precision ) ) {
			throw new IllegalValueException( '$precision needs to be an integer' );
		}

		if ( $precision < self::PRECISION_Ga || $precision > self::PRECISION_SECOND ) {
			throw new IllegalValueException( '$precision out of allowed bounds' );
		}

		if ( !is_string( $calendarModel ) ) { //XXX: enforce IRI? Or at least a size limit?
			throw new IllegalValueException( '$calendarModel needs to be a string' );
		}

		$this->time = $time;
		$this->timezone = $timezone;
		$this->before = $before;
		$this->after = $after;
		$this->precision = $precision;
		$this->calendarModel = $calendarModel;
	}

	/**
	 * @see $time
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getTime() {
		return $this->time;
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
		return $this->time;
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
		list( $time, $timezone, $before, $after, $precision, $calendarModel ) = json_decode( $value );
		$this->__construct( $time, $timezone, $before, $after, $precision, $calendarModel );
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
			'time' => $this->time,
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
		return $this->time;
	}

}
