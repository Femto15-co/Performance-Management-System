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
 	public function attachToUser($user,$defectId);
 	public function destroy($defectAttachmentId);
  
}