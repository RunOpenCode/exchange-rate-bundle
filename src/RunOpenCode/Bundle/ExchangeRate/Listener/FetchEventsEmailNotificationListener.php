<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Listener;

use RunOpenCode\Bundle\ExchangeRate\Event\FetchErrorEvent;
use RunOpenCode\Bundle\ExchangeRate\Event\FetchSuccessEvent;

/**
 * Class FetchEventsEmailNotificationListener
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Listener
 */
class FetchEventsEmailNotificationListener
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * FetchEventsEmailNotificationListener constructor.
     *
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    /**
     * Success event handler
     *
     * @param FetchSuccessEvent $successEvent
     */
    public function onFetchSuccess(FetchSuccessEvent $successEvent)
    {
        $message = $this->buildSwiftMessage('@ExchangeRate/mail/success.html.twig', [
            'date' => $successEvent->getDate(),
            'rates' => $successEvent->getRates()
        ]);

        $this->mailer->send($message);
    }

    /**
     * Error event handler
     *
     * @param FetchErrorEvent $errorEvent
     */
    public function onFetchError(FetchErrorEvent $errorEvent)
    {
        $message = $this->buildSwiftMessage('@ExchangeRate/mail/error.html.twig', [
            'date' => $errorEvent->getDate(),
            'errors' => $errorEvent->getErrors()
        ]);

        $this->mailer->send($message);
    }

    /**
     * Builds swift message
     *
     * @param string $template
     * @param array $context
     *
     * @return \Swift_Message
     */
    private function buildSwiftMessage($template, array $context = [])
    {
        $message = new \Swift_Message();

        $message
            ->setSubject($this->renderBlock($template, 'subject', $context))
            ->setTo($this->renderBlock($template, 'to', $context))
            ->setBody($this->renderBlock($template, 'body', $context), 'text/html');

        return $message;
    }

    /**
     * Render twig template single block
     *
     * @param string $template
     * @param string $block
     * @param array $context
     *
     * @return string
     */
    private function renderBlock($template, $block, array $context = [])
    {
        /** @var $template \Twig_Template */
        $template = $this->twig->loadTemplate($template);
        $context = $this->twig->mergeGlobals($context);

        return $template->renderParentBlock($block, $context);
    }
}
