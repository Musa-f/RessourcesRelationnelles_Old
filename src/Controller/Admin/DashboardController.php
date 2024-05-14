<?php

namespace App\Controller\Admin;

use App\Entity\Ressource;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('RessourcesRelationnelles');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::section("Gestion");
        yield MenuItem::linkToCrud('Ressources', 'fa-solid fa-folder-tree', Ressource::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fa-solid fa-user', User::class);

        yield MenuItem::section("Statistiques");
        yield MenuItem::linkToRoute('Ressources', 'fa-solid fa-folder-tree', 'resource_statistic');
        yield MenuItem::linkToRoute('Utilisateurs', 'fa-solid fa-user', 'user_statistic');
    }
}
