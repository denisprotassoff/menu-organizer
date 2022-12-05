<?php
declare(strict_types=1);

namespace ScandiPWA\MenuOrganizer\Api;

/**
 * @category ScandiPWA
 * @package ScandiPWA\MenuOrganizer\Api
 * @author Vladislavs Zimnikovs <info@scandiweb.com>
 * @copyright Copyright (c) 2022 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *
 * Interface ItemInterface
 */
interface MenuManagerInterface
{
    /**
     * @param int $menuId
     * @return int
     */
    public function deleteItems(int $menuId): int;
}
