<?php

namespace RunOpenCode\Bundle\ExchangeRate\Notification;

use RunOpenCode\Bundle\ExchangeRate\Contract\NotificationInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use RunOpenCode\ExchangeRate\Log\LoggerAwareTrait;

class MailNotification implements NotificationInterface
{
    use LoggerAwareTrait;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var EngineInterface
     */
    protected $templateEngine;

    /**
     * @var array
     */
    protected $settings;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $engine, array $settings)
    {
        $this->mailer = $mailer;
        $this->templateEngine = $engine;
        $this->settings = $settings;
    }

    /**
     * Create and send mail notification.
     *
     * @param array $vars Variables to use when rendering mail body.
     * @return MailNotification $this Fluent interface.
     */
    public function notify(array $vars = array())
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($this->settings['subject'])
            ->setFrom($this->settings['from'])
            ->setTo($this->settings['to'])
            ->setCc($this->settings['cc'])
            ->setBcc($this->settings['bcc'])
            ->setBody($this->templateEngine->render($this->settings['template'], $vars), 'text/html');

        try {
            $this->mailer->send($message);

            $this->getLogger()->notice('Mail notification successfully sent.', array_merge(
                $this->settings,
                array('body' => $message->getBody())
            ));
        } catch (\Exception $e) {
            $this->getLogger()->error('Could not send email notification.', array_merge(
                $this->settings,
                array('body' => $message->getBody())
            ));
        }

        return $this;
    }
}
