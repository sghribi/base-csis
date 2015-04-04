<?php

namespace CSIS\EamBundle\Listener;

use CSIS\EamBundle\Event\TagEvent;
use CSIS\EamBundle\Events;
use CSIS\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Swift_Mailer;
use Twig_Environment;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TagListener
 */
class TagListener implements EventSubscriberInterface
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
     * @var array
     */
    private $parameters;

    /**
     * @param Swift_Mailer      $mailer
     * @param RouterInterface   $router
     * @param Twig_Environment  $twig
     * @param EntityManager     $em
     * @param array             $parameters
     */
    public function __construct(
        Swift_Mailer $mailer,
        RouterInterface $router,
        Twig_Environment $twig,
        EntityManager $em,
        array $parameters
    )
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->em = $em;
        $this->parameters = $parameters;
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
        );
    }

    public function sendNewTagMessage(TagEvent $tagEvent)
    {
        $url = $this->router->generate('tag', array('onglet' => 'edit'), true);
        $template = $this->parameters['email']['template'];

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
            $template,
            $context,
            $this->parameters['email']['from_email']['address'],
            $this->parameters['email']['from_email']['sender_name'],
            $moderatorsEmails
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
