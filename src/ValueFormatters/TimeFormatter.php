<?php

namespace ValueFormatters;

use DataValues\TimeValue;
use InvalidArgumentException;

/**
 * Basic plain text formatter for TimeValue objects that either delegates formatting to an other
 * formatter given via self::OPT_TIME_ISO_FORMATTER or outputs the timestamp in simple YMD-ordered
 * fallback formats, resembling ISO 8601.
 *
 * @since 0.1
 *
 * @license GPL-2.0+
 * @author H. Snater < mediawiki@snater.com >
 * @author Thiemo Mättig
 */
class TimeFormatter extends ValueFormatterBase {

	/**
	 * @deprecated since 0.7.1, use TimeValue::CALENDAR_GREGORIAN instead
	 */
	const CALENDAR_GREGORIAN = TimeValue::CALENDAR_GREGORIAN;

	/**
	 * @deprecated since 0.7.1, use TimeValue::CALENDAR_JULIAN instead
	 */
	const CALENDAR_JULIAN = TimeValue::CALENDAR_JULIAN;

	/**
	 * Option to localize calendar models. Must contain an array mapping known calendar model URIs
	 * to localized calendar model names.
	 */
	const OPT_CALENDARNAMES = 'calendars';

	/**
	 * Option for a custom timestamp formatter. Must contain an instance of a ValueFormatter
	 * subclass, capable of formatting TimeValue objects. The output of the custom formatter is
	 * threaded as plain text and passed through.
	 */
	const OPT_TIME_ISO_FORMATTER = 'time iso formatter';

	/**
	 * @see ValueFormatterBase::__construct
	 *
	 * @param FormatterOptions|null $options
	 */
	public function __construct( FormatterOptions $options = null ) {
		parent::__construct( $options );

		// A non-localized default is still better than showing full concept URIs.
		$this->defaultOption( self::OPT_CALENDARNAMES, array(
			TimeValue::CALENDAR_GREGORIAN => 'Gregorian',
			TimeValue::CALENDAR_JULIAN => 'Julian',
		) );
		$this->defaultOption( self::OPT_TIME_ISO_FORMATTER, null );
	}

	/**
	 * @see ValueFormatter::format
	 *
	 * @param TimeValue $value
	 *
	 * @throws InvalidArgumentException
	 * @return string Plain text
	 */
	public function format( $value ) {
		if ( !( $value instanceof TimeValue ) ) {
			throw new InvalidArgumentException( 'Data value type mismatch. Expected a TimeValue.' );
		}

		$formatted = $this->getFormattedTimestamp( $value );
		// FIXME: Temporarily disabled.
		// $formatted .= ' (' . $this->getFormattedCalendarModel( $value->getCalendarModel() ) . ')';
		return $formatted;
	}

	/**
	 * @param TimeValue $value
	 *
	 * @return string Plain text
	 */
	private function getFormattedTimestamp( TimeValue $value ) {
		$formatter = $this->getOption( self::OPT_TIME_ISO_FORMATTER );

		if ( $formatter instanceof ValueFormatter ) {
			return $formatter->format( $value );
		}

		if ( !preg_match(
			// Loose check for ISO-like strings, as used in Gregorian and Julian time values.
			'/^([-+]?)(\d+)-(\d+)-(\d+)(?:T(\d+):(\d+)(?::(\d+))?)?Z?$/i',
			$value->getTime(),
			$matches
		) ) {
			return $value->getTime();
		}

		list( , $sign, $year, $month, $day, $hour, $minute, $second ) = $matches;

		// Actual MINUS SIGN (U+2212) instead of HYPHEN-MINUS (U+002D)
		$sign = $sign === '-' ? "\xE2\x88\x92" : '';

		// Warning, never cast the year to integer to not run into 32-bit integer overflows!
		$year = ltrim( $year, '0' );

		if ( $value->getPrecision() <= TimeValue::PRECISION_YEAR ) {
			return sprintf( '%s%04s', $sign, $year );
		}

		switch ( $value->getPrecision() ) {
			case TimeValue::PRECISION_MONTH:
				return sprintf(
					'%s%04s-%02s',
					$sign, $year, $month
				);
			case TimeValue::PRECISION_DAY:
				return sprintf(
					'%s%04s-%02s-%02s',
					$sign, $year, $month, $day
				);
			case TimeValue::PRECISION_HOUR:
				return sprintf(
					'%s%04s-%02s-%02sT%02s',
					$sign, $year, $month, $day, $hour
				);
			case TimeValue::PRECISION_MINUTE:
				return sprintf(
					'%s%04s-%02s-%02sT%02s:%02s',
					$sign, $year, $month, $day, $hour, $minute
				);
			default:
				return sprintf(
					'%s%04s-%02s-%02sT%02s:%02s:%02s',
					$sign, $year, $month, $day, $hour, $minute, $second
				);
		}
	}

	/**
	 * @param string $calendarModel
	 *
	 * @return string Plain text
	 */
	private function getFormattedCalendarModel( $calendarModel ) {
		$calendarNames = $this->getOption( self::OPT_CALENDARNAMES );

		if ( array_key_exists( $calendarModel, $calendarNames ) ) {
			return $calendarNames[$calendarModel];
		}

		return $calendarModel;
	}

}
