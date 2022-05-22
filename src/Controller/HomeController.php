<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
class HomeController extends AbstractController
{
        /**
         * @Route("/", name="home")
         */
        public function index(Session $session)
        {
            $return = [];
            if($session->has('message'))
            {
                $message = $session->get('message');
                $session->remove('message'); // we remove the message from the session
                $return['message'] = $message; // we add the message to the array of parameters
            }
            return $this->render('home/index.html.twig', $return);
        }

}
