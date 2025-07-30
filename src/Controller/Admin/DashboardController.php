<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ModelCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SiteBuilderAPI');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Business Customer Targets', 'fa fa-bullseye', \App\Entity\BusinessCustomerTarget::class);
        yield MenuItem::linkToCrud('Business Goals', 'fa fa-flag-checkered', \App\Entity\BusinessGoal::class);
        yield MenuItem::linkToCrud('Business Legal Statuses', 'fa fa-gavel', \App\Entity\BusinessLegalStatus::class);
        yield MenuItem::linkToCrud('Business Sectors', 'fa fa-briefcase', \App\Entity\BusinessSector::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-tags', \App\Entity\Category::class);
        yield MenuItem::linkToCrud('Global SEO', 'fa fa-globe', \App\Entity\GlobalSeo::class);
        yield MenuItem::linkToCrud('Images', 'fa fa-image', \App\Entity\Image::class);
        yield MenuItem::linkToCrud('Models', 'fa fa-cube', \App\Entity\Model::class);
        yield MenuItem::linkToCrud('Statuses', 'fa fa-check-circle', \App\Entity\Status::class);
        yield MenuItem::linkToCrud('Templates', 'fa fa-file', \App\Entity\Template::class);
        yield MenuItem::linkToCrud('Themes', 'fa fa-paint-brush', \App\Entity\Theme::class);
        yield MenuItem::linkToCrud('Users', 'fa fa-users', \App\Entity\User::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
