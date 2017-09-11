<?php

namespace App\Repositories\Sheet;

/**
 * Interface Sheet
 * Simple contract to force implementation of below functions
 */
interface SheetInterface
{
    public function getSheetsForAUserScope($isAdmin, $loggedInUserId, $userId = null);
}