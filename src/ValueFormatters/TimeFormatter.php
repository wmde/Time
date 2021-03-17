<?php

namespace ValueFormatters;

use DataValues\TimeValue;
use InvalidArgumentException;

/**
 * Basic plain text formatter for TimeValue objects that either delegates formatting to an other
 * formatter given via OPT_TIME_ISO_FORMATTER or outputs the timestamp as it is.
 *
 * @since 0.1
 *
 * @license GPL-2.0-or-later
 * @author H. Snater < mediawiki@snater.com >
 */
class TimeFormatter implements ValueFormatter {

	/**
	 * @deprecated since 0.7.1, use TimeValue::CALENDAR_GREGORIAN instead
	 */
	public const CALENDAR_GREGORIAN = TimeValue::CALENDAR_GREGORIAN;

	/**
	 * @deprecated since 0.7.1, use TimeValue::CALENDAR_JULIAN instead
	 */
	public const CALENDAR_JULIAN = TimeValue::CALENDAR_JULIAN;

	/**
	 * Option to localize calendar models. Must contain an array mapping known calendar model URIs
	 * to localized calendar model names.
	 */
	public const OPT_CALENDARNAMES = 'calendars';

	/**
	 * Option for a custom timestamp formatter. Must contain an instance of a ValueFormatter
	 * subclass, capable of formatting TimeValue objects. The output of the custom formatter is
	 * threaded as plain text and passed through.
	 */
	public const OPT_TIME_ISO_FORMATTER = 'time iso formatter';

	/**
	 * @var FormatterOptions
	 */
	private $options;

	/**
	 *
	 * @param FormatterOptions|null $options
	 */
	public function __construct( FormatterOptions $options = null ) {
		$this->options = $options ?: new FormatterOptions();

		$this->options->defaultOption( self::OPT_CALENDARNAMES, array() );
		$this->options->defaultOption( self::OPT_TIME_ISO_FORMATTER, null );
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

		$formatted = $value->getTime();

		$isoFormatter = $this->options->getOption( self::OPT_TIME_ISO_FORMATTER );
		if ( $isoFormatter instanceof ValueFormatter ) {
			$formatted = $isoFormatter->format( $value );
		}

		return $formatted;
	}

}
