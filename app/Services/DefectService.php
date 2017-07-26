<?php
namespace App\Services;

use App\Repositories\Defect\DefectInterface;
use App\Repositories\User\UserInterface;
use App\Repositories\Comment\CommentInterface;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DefectService
{
    public $defectRepository;
    public $commentRepository;

    public function __construct(
        DefectInterface $defectRepository,
        UserInterface $userRepository,
        CommentInterface $commentRepository
        )
    {
        $this->defectRepository = $defectRepository;
        $this->userRepository = $userRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * Generates dataTable controllers
     * @param $userId
     * @param $defect
     * @return string
     */
    public function dataTableControllers($userId, $defect)
    {
        $formHead = "<form class='delete-form' method='POST' action='" . route('defect.destroy', $defect->id) . "'>" . csrf_field();
        $editLink = "<a href=" . route('defect.edit', [$userId, $defect->id]) . " class='btn btn-xs btn-primary'>".
            "<i class='glyphicon glyphicon-edit'></i>" . trans('general.edit') . "</a>";
        $deleteForm =
            "  <input type='hidden' name='_method' value='DELETE'/>
                            <button type='submit' class='btn btn-xs btn-danger main_delete'>
                                <i class='glyphicon glyphicon-trash'></i> " . trans('general.delete') . "
                            </button>
                        </form>";

        return $formHead . $editLink . $deleteForm;
    }

    /**
     * Create comment and return its id
     * @param string $comment comment text
     * @return Comment  
     */
    public function addComment($comment, $user_id)
    {
        //Create Comment
        $comment_created = $this->commentRepository->addItem([
            'comment' => $comment,
            'user_id' => $user_id
            ]);
        return $comment_created;
    }
    /**
     * get Comment by defect_user id
     * @param  integer $defectAttachmentId defect_user id
     * @return Comment  comment object
     */
    public function getComment($defectAttachmentId)
    {
        //get comment id from defect_user id
        $commentId = $this->defectRepository->getCommentId($defectAttachmentId);

        if(!$commentId)
            return false;

        //get comment object with comment id
        $comment = $this->commentRepository->getItem($commentId);
        return $comment;
    }

    /**
     * updates comment on defect and adds it if not existed before
     * @param  integer $defectAttachmentId defect_user id
     * @param  string $comment            comment text
     * @param  integer $user_id            logged in user
     * @return mixed
     */
    public function updateComment($defectAttachmentId, $comment, $user_id)
    {
        //get comment id from defect_user id
        $commentId = $this->defectRepository->getCommentId($defectAttachmentId);
        //checks if there is a comment before
        if(!$commentId){
            //no comment before and no new comment
            if(empty($comment))
                return null;
            //create new comment
            $comment_created = $this->commentRepository->addItem([
                'comment' => $comment,
                'user_id' => $user_id
                ]);
            return $comment_created->id;
        }
        //update comment
        $this->commentRepository->editItem($commentId,['comment'=> $comment]);
        return $commentId;
    }
}