<?php

namespace AppBundle\Controller;

use AppBundle\Repository\UserRepository;
use AppBundle\Repository\ApiTokenRepository;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
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

    protected function createApiResponse($data, $statusCode = 200) {
        $json = $this->serialize($data);

        return new Response($json, $statusCode, array('Content-Type' => 'application/json'));
    }

    protected function serialize($data, $format = 'json') {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $request = $this->get('request_stack')->getCurrentRequest();
        $groups = array('Default');
        if ($request->query->get('deep')) {
            $groups[] = 'deep';
        }
        $context->setGroups($groups);

        return $this->container->get('jms_serializer')->serialize($data, $format, $context);
    }
}
