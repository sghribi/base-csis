<?php

namespace CSIS\EamBundle\Listener;

use CSIS\EamBundle\Event\TagEvent;
use CSIS\EamBundle\Event\UserEvent;
use CSIS\EamBundle\Events;
use CSIS\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Util\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Swift_Mailer;
use Twig_Environment;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class EmailListener
 */
class EmailListener implements EventSubscriberInterface
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var array
     */
    private $parametersTag;

    /**
     * @var array
     */
    private $parametersWelcome;

    /**
     * @param Swift_Mailer      $mailer
     * @param RouterInterface   $router
     * @param Twig_Environment  $twig
     * @param EntityManager     $em
     * @param array             $parametersTag
     * @param array             $parametersWelcome
     */
    public function __construct(
        Swift_Mailer $mailer,
        RouterInterface $router,
        Twig_Environment $twig,
        EntityManager $em,
        TokenGenerator $tokenGenerator,
        array $parametersTag,
        array $parametersWelcome
    )
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->em = $em;
        $this->tokenGenerator = $tokenGenerator;
        $this->parametersTag = $parametersTag;
        $this->parametersWelcome = $parametersWelcome;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::TAG_CREATED => array(
                array('sendNewTagMessage')
            ),
            Events::USER_ENABLED => array(
                array('sendUserEnabledMessage')
            ),
        );
    }

    /**
     * @param TagEvent $tagEvent
     */
    public function sendNewTagMessage(TagEvent $tagEvent)
    {
        $url = $this->router->generate('tag', array('onglet' => 'edit'), true);

        $context = array(
            'tagCreator'      => $tagEvent->getUser(),
            'tag'             => $tagEvent->getTag(),
            'equipment'       => $tagEvent->getEquipment(),
            'url'             => $url
        );

        //@TODO
        /** @var User[] $moderators */
        $moderators = $this->em->getRepository('CSISUserBundle:User')->findByRole('ROLE_GEST_TAGS');

        $moderatorsEmails = array();

        foreach ($moderators as $moderator) {
            $moderatorsEmails[$moderator->getEmail()] = sprintf('%s %s', $moderator->getFirstName(), $moderator->getLastName());
        }

        $this->sendMessage(
            $this->parametersTag['email']['template'],
            $context,
            $this->parametersTag['email']['from_email']['address'],
            $this->parametersTag['email']['from_email']['sender_name'],
            $moderatorsEmails
        );

    }

    /**
     * @param UserEvent $userEvent
     */
    public function sendUserEnabledMessage(UserEvent $userEvent)
    {
        /** @var User $user */
        $user = $userEvent->getUser();

        if (null === $user->getConfirmationToken()) {
            $tokenGenerator = $this->tokenGenerator;
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }
        $user->setPasswordRequestedAt(new \DateTime());

        $url = $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), true);
        $baseUrl = $this->router->generate('csis_eam_homepage', array(), true);

        $context = array(
            'user'      => $user,
            'url'       => $url,
            'baseUrl'   => $baseUrl,
        );

        $this->sendMessage(
            $this->parametersWelcome['email']['template'],
            $context,
            $this->parametersWelcome['email']['from_email']['address'],
            $this->parametersWelcome['email']['from_email']['sender_name'],
            $user->getEmailCanonical()
        );
    }

    /**
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $senderName
     * @param string $toEmail
     */
    protected function sendMessage($templateName, $context, $fromEmail, $senderName, $toEmail)
    {
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail, $senderName)
            ->setSender($fromEmail, $senderName)
            ->setTo($toEmail);
        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }
        $this->mailer->send($message);
    }
}
