<?php

namespace App\Controllers;

use Azera\Http\Response;

class IndexController extends \Azera\Core\Controller
{
    public function indexAction(): string|Response
    {
        return $this->view()->render('home', [
            'title'   => 'Welcome to Azera Framework',
            'message' => 'Your application is ready!'
        ]);
    }

    public function aboutAction(): string|Response
    {
        return $this->view()->render('about', [
            'title' => 'About - Azera Framework'
        ]);
    }
}