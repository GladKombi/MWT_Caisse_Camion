<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class RapportController extends Controller
{
    public function index(): void
    {
        $this->view('rapports/index');
    }
}
