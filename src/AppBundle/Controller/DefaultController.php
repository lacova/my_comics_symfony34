<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Controller\SecurityController as userBase;

class DefaultController extends userBase
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, AuthorizationCheckerInterface $authorizationChecker)
    {
        // get the login error if there is one
        if ($authorizationChecker->isGranted('ROLE_ADMIN')===false && $authorizationChecker->isGranted('ROLE_USER')===false) {
            return $this->redirect('/login', 301);
        } else if ($authorizationChecker->isGranted('ROLE_USER')===true) {
            var_dump('hola');
        } else {
            return $this->redirect('/admin', 301);
        }
    }
}
