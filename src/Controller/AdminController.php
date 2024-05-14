<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Ressource;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class AdminController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/admin/dashboard/statistic/user', name: 'user_statistic')]
    public function user_statistic(ChartBuilderInterface $chartBuilder): Response
    {
        $currentYear = date('Y');

        $labels = [];
        $data = [];
    
        $users = $this->em->getRepository(User::class)->findAll();

        foreach($users as $user) {
            $creationDate = $user->getCreationDate()->format('d/m/Y');
            if(!isset($data[$creationDate])) {
                $data[$creationDate] = 0;
            }
            $data[$creationDate]++;
        }
    
        foreach ($data as $date => $count) {
            $labels[] = $date;
            $dataPoints[] = $count;
        }
    
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Nombre de comptes créés',
                    'backgroundColor' => 'rgb(213 165 7)',
                    'borderColor' => 'rgb(82, 150, 167)',
                    'data' => $dataPoints,
                ],
            ],
        ]);

        return $this->render('admin/user_statistic.html.twig', [
            'chart' => $chart
        ]);
    }

    #[Route('/admin/dashboard/statistic/resource', name: 'resource_statistic')]
    public function resource_statistic(ChartBuilderInterface $chartBuilder): Response
    {
        $categories = $this->em->getRepository(Category::class)->findAll();

        $totalResources = $this->em->getRepository(Ressource::class)->count([]);
    
        $labels = [];
        $data = [];

        foreach ($categories as $category) {
            $resourceCount = $this->em->getRepository(Ressource::class)->count(['category' => $category->getId()]);
            $percentage = ($resourceCount / $totalResources) * 100;
            $labels[] = $category->getName();
            $data[] = $percentage;
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pourcentage',
                    'backgroundColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                    ],
                    'data' => $data,
                ],
            ],
        ]);
        
        return $this->render('admin/resource_statistic.html.twig', [
            'chart' => $chart,
        ]);
    }
}
