<?php
/**
 * Trait utilitaire pour jQuery Colorbox Dafap Edition
 *
 * @package   jquery-colorbox-dafap-edition
 * @author    Alain Pomirol (Dafap)
 * @license   GPL-2.0-or-later
 * @file      includes/Traits/UtilsTrait.php
 */

declare(strict_types=1);

namespace Dafap\JQueryColorbox\Traits;

trait UtilsTrait
{
    /**
     * Vérifie si un paramètre de réglage équivaut à un booléen vrai ('true' ou true).
     *
     * @ Clé du réglage à tester dans le tableau $this->colorboxSettings.
     * @param string $key Clé du réglage.
     * @return bool Vrai si la valeur est active, faux sinon.
     */
    protected function isTrue(string $key): bool
    {
        if (!isset($this->colorboxSettings[$key])) {
            return false;
        }

        $val = $this->colorboxSettings[$key];

        return $val === true || $val === 'true' || $val === 1 || $val === '1';
    }
}