<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;


class UserController extends AbstractController
{
   
    /**
     * @Route("user/default", name="get_default_user")
     */
    public function getDefaultUser(UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(['id' => 1]);

        $listUser = [];

        if ($user) {
            $listUser[] = [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'phone' => $user->getPhone(),
                'birthday' => $user->getBirthDate(),
                'nationality' => $user->getNationality(),
                'administrator' => $user->isAdministrator(),
            ];
        }

        return $this->json([
            'user' => $listUser,
        ]);
    }
}
