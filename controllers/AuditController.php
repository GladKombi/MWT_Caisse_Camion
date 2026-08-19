<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class AuditController extends Controller
{
    public function index(): void
    {
        $this->view('audit/index');
    }
}
