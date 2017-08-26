<?php

namespace AppBundle\Controller;

use AppBundle\Repository\UserRepository;
use AppBundle\Repository\ApiTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AppBundle\Entity\User;

abstract class BaseController extends Controller {
    /**
     * Is the current user logged in?
     *
     * @return boolean
     */
    public function isUserLoggedIn() {
        return $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY');
    }

    /**
     * Logs this user into the system
     *
     * @param User $user
     */
    public function loginUser(User $user) {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());

        $this->container->get('security.token_storage')->setToken($token);
    }

    /**
     * Used to find the fixtures user - I use it to cheat in the beginning
     *
     * @param $username
     * @return User
     */
    public function findUserByUsername($username) {
        return $this->getUserRepository()->findUserByUsername($username);
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:User');
    }

    /**
     * @return ApiTokenRepository
     */
    protected function getApiTokenRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:ApiToken');
    }
}
