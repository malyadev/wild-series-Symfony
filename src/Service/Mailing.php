<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Response;

class Mailing
{
    public function newMail()
    {
        return $this->renderView('mail.html.twig');
    }
}
