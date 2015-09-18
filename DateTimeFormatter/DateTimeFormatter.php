<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\DateTimeFormatter;

use DateTime;
use Knp\Bundle\TimeBundle\DateTimeFormatter as BaseFormatter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Formats a DateTime object
 */
class DateTimeFormatter
{
    /**
     * Request object to get the current locale.
     *
     * @var Request
     */
    protected $request;

    /**
     * DateTime formatter.
     *
     * @var BaseFormatter
     */
    protected $formatter;

    /**
     * @param BaseFormatter $formatter
     */
    public function __construct(BaseFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    public function format(DateTime $dateTime)
    {
        $now = new DateTime;

        // If the date is less then a day ago, return 'ago' time.
        if ($now->diff($dateTime)->d < 1) {
            return $this->formatter->formatDiff($dateTime, $now);
        }

        // Return localized date and time.
        $locale        = $this->request ? $this->request->getLocale() : 'en_US';
        $dateFormatter = \IntlDateFormatter::SHORT;
        $timeFormatter = \IntlDateFormatter::SHORT;
        $timezone      = date_default_timezone_get();

        $formatter = datefmt_create($locale, $dateFormatter, $timeFormatter, $timezone, \IntlDateFormatter::GREGORIAN);

        // Convert to timestamp to provide compatibility with early PHP 5.3 versions.
        return datefmt_format($formatter, date_timestamp_get($dateTime));
    }
}
