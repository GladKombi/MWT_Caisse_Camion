<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/MouvementCaisse.php';

class CaisseController extends Controller
{
    public function index(): void
    {
        $model=new MouvementCaisse();if($_SERVER['REQUEST_METHOD']==='POST'){$this->closeVehicle($model);return;}$mouvements=$model->getLedger();$situation=$model->getSituation();$evolution=$model->getDailyEvolution();$vehiculesACloturer=$model->getClosableVehicles();
        $message=$_SESSION['cash_message']??null;$messageType=$_SESSION['cash_message_type']??'success';unset($_SESSION['cash_message'],$_SESSION['cash_message_type']);
        $this->view('caisse/index',compact('mouvements','situation','evolution','vehiculesACloturer','message','messageType'));
    }
    private function closeVehicle(MouvementCaisse$model):void
    {
        try{$vehicleId=(int)($_POST['vehicule_id']??0);if(($_POST['action']??'')!=='close_vehicle'||$vehicleId<=0)throw new RuntimeException('Véhicule invalide.');$result=$model->closeVehicleAndPay($vehicleId);$_SESSION['cash_message']="Clôture #{$result['cloture_id']} effectuée : {$result['recettes']} recette(s), {$result['depenses']} dépense(s) et {$result['remunerations']} rémunération(s) pour {$result['travailleurs']} travailleur(s).";$_SESSION['cash_message_type']='success';}catch(Throwable$e){$_SESSION['cash_message']=$e->getMessage();$_SESSION['cash_message_type']='error';}$this->redirect('/caisse');
    }
}
