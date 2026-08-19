<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/MouvementCaisse.php';

class CaisseController extends Controller
{
    public function index(): void
    {
        $model=new MouvementCaisse();$mouvements=$model->getLedger();$situation=$model->getSituation();$evolution=$model->getDailyEvolution();
        $this->view('caisse/index',compact('mouvements','situation','evolution'));
    }
}
