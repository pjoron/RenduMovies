<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Extra\Intl\IntlExtension;
use App\Service\Slugify;
use App\Event\EntitySavedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface; 

class DemoController extends AbstractController
{
    private $dispatcher;
    private $logger; 

    // Injectez le service EventDispatcherInterface et LoggerInterface dans le contrôleur
    public function __construct(EventDispatcherInterface $dispatcher, LoggerInterface $logger)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;

        $this->dispatcher->addListener(EntitySavedEvent::class, function (EntitySavedEvent $event) {
            $movie = $event->getMovie();
            // Ajoutez une entrée aux logs
            $this->logger->info('A new movie was saved: ' . $movie->getTitle());
        });
    }

    #[Route('/demo', name: 'app_demo')]
    public function index(Slugify $slugify): Response
    {
        $slug = $slugify->slugify('Ceci est un test');
        return $this->render('demo/index.html.twig', [
            'controller_name' => 'DemoController',
            'slug' => $slug,
        ]);
    }
}
