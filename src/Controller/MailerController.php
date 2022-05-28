<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    /**
     * Send email
     *
     * @Route("/", name="send_email")
     * @param MailerInterface $mailer
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function sendEmail(MailerInterface $mailer, Request $request): Response
    {
        $emailTo = $request->get('sender_email');
        if ($emailTo) {
            $email = (new Email())
                ->from('no-reply@snowtricks.fr')
                ->to($emailTo)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                ->priority(Email::PRIORITY_NORMAL)
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');

            $mailer->send($email);
        }
        return $this->redirectToRoute('figure_index', [], Response::HTTP_SEE_OTHER);
    }
}