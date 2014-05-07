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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Bundle\TimeBundle\DateTimeFormatter as BaseFormatter;
use DateTime;

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
     * Constructor
     *
     * @param ContainerInterface $container Service container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->request   = $container->get('request');
        $this->formatter = $container->get('time.datetime_formatter');
    }

    public function format(DateTime $dateTime)
    {
        $now = new DateTime;

        // If the date is less then a day ago, return 'ago' time.
        if ($now->diff($dateTime)->d < 1) {
            return $this->formatter->formatDiff($dateTime, $now);
        }

        // Return localized date and time.
        $locale        = $this->request->getLocale();
        $dateFormatter = \IntlDateFormatter::SHORT;
        $timeFormatter = \IntlDateFormatter::SHORT;
        $timezone      = date_default_timezone_get();

        $formatter = datefmt_create($locale, $dateFormatter, $timeFormatter, $timezone, \IntlDateFormatter::GREGORIAN);

        // Convert to timestamp to provide compatibility with early PHP 5.3 versions.
        return datefmt_format($formatter, date_timestamp_get($dateTime));
    }
}
