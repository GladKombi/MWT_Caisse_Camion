<?php
require_once dirname(__DIR__).'/core/Controller.php';require_once dirname(__DIR__).'/models/EspaceEmploye.php';
class EspaceEmployeController extends Controller
{
    public function index():void
    {
        if(($_SESSION['user']['role']??'')!=='EMPLOYE'){$this->redirect('/dashboard');return;}$workerId=(int)($_SESSION['user']['travailleur_id']??0);$worker=$mouvements=$estimations=[];$acquises=['USD'=>0,'CDF'=>0];$liaisonManquante=$workerId<=0;
        if(!$liaisonManquante){$model=new EspaceEmploye();$worker=$model->getWorker($workerId);$mouvements=$model->getMovements($workerId);$estimations=$model->getEstimates($workerId);$acquises=$model->getEarned($workerId);$liaisonManquante=!$worker;}
        $this->view('employe/index',compact('worker','mouvements','estimations','acquises','liaisonManquante'));
    }
}
