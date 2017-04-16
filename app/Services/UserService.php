<?php
namespace App\Services;

use App\Repositories\User\UserInterface;

class UserService
{
    public $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function isEmployee($user)
    {
        //Ensure that selected employee has employee rule
        if(!$user->hasRole('employee'))
        {
            throw new \Exception(trans('reports.no_employee'));
        }
    }
}