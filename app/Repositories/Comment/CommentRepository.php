<?php

namespace App\Repositories\Comment;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use \App\Report;

/**
* 
*/
class CommentRepository extends BaseRepository implements CommentInterface
{
	
    public function __construct(Model $comment)
    {
        $this->setModel($comment);
        $this->originalModel = $this->getModel();
    }

}