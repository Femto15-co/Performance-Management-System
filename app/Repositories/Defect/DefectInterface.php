<?php

namespace App\Repositories\Defect;

/**
 * Interface DefectInterface
 * Simple contract to force implementation of below functions
 */
interface DefectInterface
{

	 /**
     * get Comment id by defect_user id
     * @param  integer $defectAttachmentId defect_user id
     * @return integer  comment id
     */
    public function getCommentId($defectAttachmentId);
}