<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\OrganizerProfile;
use App\Entity\Event;
use App\Entity\TicketType;
use App\Entity\Booking;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AsDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AsDashboard(name: 'admin', path: '/admin')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->container->get(AdminUrlGenerator::class)
            ->setController(UserCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('BookSwift Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Organizers', 'fas fa-users', OrganizerProfile::class);
        yield MenuItem::linkToCrud('Events', 'fas fa-calendar', Event::class);
        yield MenuItem::linkToCrud('Ticket Types', 'fas fa-ticket-alt', TicketType::class);
        yield MenuItem::linkToCrud('Bookings', 'fas fa-book', Booking::class);
    }
}
