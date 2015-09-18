<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Model;

interface LocaleManagerInterface
{
    /**
     * @return LocaleInterface
     */
    public function createLocale();

    /**
     * @return LocaleInterface[]
     */
    public function findLocales();

    /**
     * @param array $criteria
     *
     * @return LocaleInterface
     */
    public function findLocaleBy(array $criteria);

    /**
     * @param string $id
     *
     * @return LocaleInterface
     */
    public function findLocaleById($id);

    /**
     * @param LocaleInterface $locale
     * @param bool $flush
     */
    public function updateLocale(LocaleInterface $locale, $flush = true);

    /**
     * @param LocaleInterface $locale
     */
    public function deleteLocale(LocaleInterface $locale);

    /**
     * Get entity namespace and className
     *
     * @return string
     */
    public function getClass();
}