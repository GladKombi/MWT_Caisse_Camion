<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class FonctionController extends Controller
{
    public function index(): void
    {
        $this->view('fonctions/index');
    }
}
