<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserService
{
    public function __construct
    (
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->findAll();
    }

    public function getCurrentUser()
    {
        /*return $this->userRepository->*/
    }
}