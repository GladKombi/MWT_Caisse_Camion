<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/AffectationVehicule.php';
require_once dirname(__DIR__) . '/models/AttributionFonction.php';

class AffectationVehiculeController extends Controller
{
    public function index(): void
    {
        $model = new AffectationVehicule(); $attributionModel = new AttributionFonction();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { $this->handlePost($model, $attributionModel); return; }
        $affectations = $model->getAll(); $vehicules = $model->getVehicules(); $attributions = $model->getAttributions();
        $attributionsFonctions = $attributionModel->getAll(); $travailleurs = $attributionModel->getTravailleurs(); $fonctions = $attributionModel->getFonctions();
        $message = $_SESSION['assignment_message'] ?? null; $messageType = $_SESSION['assignment_message_type'] ?? 'success';
        unset($_SESSION['assignment_message'], $_SESSION['assignment_message_type']);
        $this->view('affectations/index', compact('affectations','vehicules','attributions','attributionsFonctions','travailleurs','fonctions','message','messageType'));
    }

    private function handlePost(AffectationVehicule $model, AttributionFonction $attributionModel): void
    {
        $action=$_POST['action']??'';$id=(int)($_POST['id']??0);
        try {
            if(str_starts_with($action,'attribution_')){$this->handleAttribution($attributionModel,$action,$id);}
            elseif($action==='delete'&&$id>0){$model->delete($id);$this->message('Affectation supprimée avec succès.');}
            else{$data=$this->validatedData();if($model->hasConflict($data['vehicule_id'],$data['attribution_id'],$data['date_debut'],$data['date_fin']?:null,$id))throw new RuntimeException('Cette période chevauche une affectation existante du véhicule ou du travailleur.');
                if($action==='create'){$model->create($data);$this->message('Affectation créée avec succès.');}
                elseif($action==='update'&&$id>0){$model->update($id,$data);$this->message('Affectation modifiée avec succès.');}else throw new RuntimeException('Action invalide.');}
        }catch(Throwable $e){$this->message($e->getMessage(),'error');}
        $this->redirect('/affectations');
    }

    private function handleAttribution(AttributionFonction $model,string $action,int $id):void
    {
        if($action==='attribution_delete'&&$id>0){$model->suspend($id);$this->message('Attribution suspendue avec succès.');return;}
        $travailleur=(int)($_POST['travailleur_id']??0);$fonction=(int)($_POST['fonction_id']??0);$taux=(float)($_POST['taux_remuneration']??0);$statut=$_POST['statut']??'ACTIF';
        if($travailleur<=0||$fonction<=0)throw new RuntimeException('Le travailleur et la fonction sont obligatoires.');
        if($taux<0)throw new RuntimeException('Le taux de rémunération ne peut pas être négatif.');
        if(!in_array($statut,['ACTIF','SUSPENDU'],true))throw new RuntimeException('Le statut est invalide.');
        if($model->exists($travailleur,$fonction,$id))throw new RuntimeException('Cette fonction est déjà attribuée à ce travailleur.');
        $data=['travailleur_id'=>$travailleur,'fonction_id'=>$fonction,'taux_remuneration'=>$taux?:null,'statut'=>$statut];
        if($action==='attribution_create'){$model->create($data);$this->message('Fonction attribuée avec succès.');}
        elseif($action==='attribution_update'&&$id>0){$model->update($id,$data);$this->message('Attribution modifiée avec succès.');}
        else throw new RuntimeException('Action d’attribution invalide.');
    }

    private function validatedData(): array
    {
        $vehicule=(int)($_POST['vehicule_id']??0);$attribution=(int)($_POST['attribution_id']??0);$debut=trim($_POST['date_debut']??'');$fin=trim($_POST['date_fin']??'');
        if($vehicule<=0||$attribution<=0||$debut==='')throw new RuntimeException('Le véhicule, le travailleur et la date de début sont obligatoires.');
        if($fin!==''&&$fin<$debut)throw new RuntimeException('La date de fin doit être postérieure à la date de début.');
        return ['vehicule_id'=>$vehicule,'attribution_id'=>$attribution,'date_debut'=>$debut,'date_fin'=>$fin,'observation'=>trim($_POST['observation']??'')];
    }
    private function message(string $message,string $type='success'):void{$_SESSION['assignment_message']=$message;$_SESSION['assignment_message_type']=$type;}
}
