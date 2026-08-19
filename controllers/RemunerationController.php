<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class RemunerationController extends Controller
{
    public function index(): void
    {
        $this->view('remunerations/index');
    }
}
