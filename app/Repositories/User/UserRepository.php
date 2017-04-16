<?php

namespace App\Repositories\User;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;

/**
 * UserRepository is a class that contains common queries for users
 */
class UserRepository extends BaseRepository implements UserInterface
{

    /**
     * Holds user model
     * @var Model
     */
    protected $userModel;

    /**
     * UserRepository constructor.
     * Inject whatever passed model
     * @param Model $user
     */
    public function __construct(Model $user)
    {
        $this->userModel = $user;
    }

    /**
     * Retrieves user by id
     * @param $id
     * @return Model user
     * @throws \Exception
     */
    public function getUserById($id)
    {
        $user = $this->userModel->find($id);

        if(!$user)
        {
            throw new \Exception(trans('users.no_employee'));
        }
        return $user;
    }

    /**
     * Get All users with role employee
     * @return \App\User[]
     * @throws \Exception
     */
    public function getAllEmployees()
    {
        $employees = $this->userModel->whereHas('roles', function($q){
            $q->where('name','=','employee');
        })->get();

        if(!$employees || $employees->isEmpty())
        {
            throw new \Exception(trans('reports.no_employee'));
        }

        return $employees;
    }
}