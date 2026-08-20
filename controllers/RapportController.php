<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Rapport.php';

class RapportController extends Controller
{
    public function index(): void
    {
        $start=$_GET['debut']??date('Y-m-01');$end=$_GET['fin']??date('Y-m-d');$vehicleId=max(0,(int)($_GET['vehicule_id']??0));
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$start))$start=date('Y-m-01');if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$end))$end=date('Y-m-d');if($start>$end)[$start,$end]=[$end,$start];
        $model=new Rapport();$vehicules=$model->getVehicles();$resume=$model->getSummary($start,$end,$vehicleId);$performances=$model->getVehiclePerformance($start,$end,$vehicleId);$categories=$model->getExpenseCategories($start,$end,$vehicleId);$remunerations=$model->getRemunerations($start,$end,$vehicleId);$clotures=$model->getClosures($start,$end,$vehicleId);
        $this->view('rapports/index',compact('start','end','vehicleId','vehicules','resume','performances','categories','remunerations','clotures'));
    }
}
