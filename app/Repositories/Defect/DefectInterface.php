<?php

namespace App\Repositories\Defect;

/**
 * Interface BonusInterface
 * Simple contract to force implementation of below functions
 */
interface DefectInterface
{
    public function getAll();
 	public function update($userId, $defectAttachmentId,$requestDefect);
  
}