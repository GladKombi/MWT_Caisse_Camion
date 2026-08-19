<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Recette.php';

class RecetteController extends Controller
{
    public function index(): void
    {
        $model=new Recette();if($_SERVER['REQUEST_METHOD']==='POST'){$this->handlePost($model);return;}
        $recettes=$model->getAll();$affectations=$model->getActiveAssignments();$totaux=$model->getTotals();
        $message=$_SESSION['income_message']??null;$messageType=$_SESSION['income_message_type']??'success';unset($_SESSION['income_message'],$_SESSION['income_message_type']);
        $this->view('recettes/index',compact('recettes','affectations','totaux','message','messageType'));
    }
    private function handlePost(Recette $model):void
    {
        $action=$_POST['action']??'';$id=(int)($_POST['id']??0);
        try{if($action==='close'&&$id>0){$model->close($id);$this->message('Recette clôturée avec succès.');}
            elseif($action==='delete'&&$id>0){$model->delete($id);$this->message('Recette supprimée avec succès.');}
            else{$d=$this->validatedData();if($action==='create'){$model->create($d);$this->message('Recette enregistrée avec succès.');}elseif($action==='update'&&$id>0){$model->update($id,$d);$this->message('Recette modifiée avec succès.');}else throw new RuntimeException('Action invalide.');}}
        catch(Throwable $e){$this->message($e->getMessage(),'error');}$this->redirect('/recettes');
    }
    private function validatedData():array
    {
        $aff=(int)($_POST['affection_vehicule']??0);$date=trim($_POST['date_recette']??'');$montant=(float)($_POST['montant']??0);$devise=$_POST['devise']??'USD';
        if($aff<=0||$date==='')throw new RuntimeException('L’affectation et la date sont obligatoires.');if($montant<=0)throw new RuntimeException('Le montant doit être supérieur à zéro.');if(!in_array($devise,['USD','CDF'],true))throw new RuntimeException('La devise est invalide.');
        return ['affection_vehicule'=>$aff,'date_recette'=>$date,'montant'=>$montant,'devise'=>$devise,'description'=>trim($_POST['description']??'')];
    }
    private function message(string $m,string $t='success'):void{$_SESSION['income_message']=$m;$_SESSION['income_message_type']=$t;}
}
