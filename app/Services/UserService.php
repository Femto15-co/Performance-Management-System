<?php
namespace App\Services;

use App\Repositories\User\UserInterface;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Ensure that user has employee rule
     * @param $user
     * @throws \Exception
     */
    public function onlyEmployee($user)
    {
        //Ensure that selected employee has employee rule
        if(!$user->hasRole('employee'))
        {
            throw new \Exception(trans('reports.no_employee'));
        }
    }

    /**
     * return selected user if user attempting is admin
     * or return logged in user, Only admin can pick whatever id they like
     * @param $userId
     * @return integer userId
     */
    public function getLoggedOrSelected($userId)
    {
        if (!Auth::user()->hasRole('admin'))
            return Auth::user();

        return $this->userRepository->getUserById($userId);
    }
}