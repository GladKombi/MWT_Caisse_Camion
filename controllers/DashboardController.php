<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Dashboard.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        $dashboard = new Dashboard();
        $stats = $dashboard->getStats();
        $evolution = $dashboard->getEvolution();
        $mouvements = $dashboard->getMouvementsRecents();
        $this->view('dashboard/index', compact('stats', 'evolution', 'mouvements'));
    }
}
