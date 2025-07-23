<?php

namespace App\Controller\Contact;

use App\Entity\SupportMessage;
use App\Form\SupportMessageType;
use App\Service\Contact\ContactService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact_us')]
    public function contact(Request $request, EntityManagerInterface $em, ContactService $contactService)
    {
        $supportMessage = new SupportMessage();
        $form = $this->createForm(SupportMessageType::class, $supportMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($supportMessage);
            $em->flush();

            // Send email
            $contactService->sendToAdmins($supportMessage);

            $this->addFlash('success', 'Your message has been sent!');
            return $this->redirectToRoute('contact_us');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
            'supportMessage' => $supportMessage, 
        ]);
    }
}