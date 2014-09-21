<?php

namespace CSIS\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

use CSIS\UserBundle\Form\AddLabType;
use CSIS\UserBundle\Form\AddInstitutionType;

class RegistrationController extends BaseController
{
    public function registerAction()
    {
        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $em = $this->container->get('doctrine')->getManager();
                
        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();
            $curUser = $this->container->get('security.context')->getToken()->getUser();

            if ($confirmationEnabled && !$user->isEnabled()) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $route = 'fos_user_registration_confirmed';
            }
            // If the user isn't admin, the user must have a dependancy for an institution or a lab
            if ( !$user->hasRole('ROLE_ADMIN') ) {
                if ( $user->hasRole('ROLE_GEST_ESTAB')) {
                    // do nothing
                } else if ( $user->hasRole('ROLE_GEST_LAB') ) {
                    $route = 'csis_user_add_institution';
                } else {
                    $route = 'csis_user_add_lab';
                }
            }

            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route);
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
        ));
    }
    
    public function addLaboratoryDependanceAction(Request $request) {
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
        $email = $this->container->get('session')->get('fos_user_send_confirmation_email/email');
        $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);
        $form = $this->container->get('form.factory')->create(
                new AddLabType($this->container->get('security.context')->getToken()->getUser()),
                $user
        );
        
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            
            if ($form->isValid()) {
                if ($confirmationEnabled) {
                    $route = 'fos_user_registration_check_email';
                } else {
                    $route = 'fos_user_registration_confirmed';
                }
                $url = $this->container->get('router')->generate($route);
                $response = new RedirectResponse($url);

                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updateUser($user);

                return $response;
            }
        }
        
        return $this->container->get('templating')->renderResponse('CSISUserBundle:Registration:addLab.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }
    
    public function addInstitutionDependanceAction(Request $request) {
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
        $email = $this->container->get('session')->get('fos_user_send_confirmation_email/email');
        $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);
        $form = $this->container->get('form.factory')->create(
                new AddInstitutionType($this->container->get('security.context')->getToken()->getUser()),
                $user
        );
        
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            
            if ($form->isValid()) {
                if ($confirmationEnabled) {
                    $route = 'fos_user_registration_check_email';
                } else {
                    $route = 'fos_user_registration_confirmed';
                }
                $url = $this->container->get('router')->generate($route);
                $response = new RedirectResponse($url);

                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updateUser($user);

                return $response;
            }
        }
        
        return $this->container->get('templating')->renderResponse('CSISUserBundle:Registration:addInstitution.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }
}
