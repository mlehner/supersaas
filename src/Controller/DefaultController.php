<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class DefaultController extends AbstractController
{
    public function index(Environment $twig): Response
    {
        return Response::create($twig->render('index.html.twig'));
    }
}