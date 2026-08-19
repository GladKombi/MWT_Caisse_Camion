<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Depense.php';
require_once dirname(__DIR__) . '/models/CategorieDepense.php';

class DepenseController extends Controller
{
    public function index(): void
    {
        $model=new Depense();$categoryModel=new CategorieDepense();if($_SERVER['REQUEST_METHOD']==='POST'){$this->handlePost($model,$categoryModel);return;}
        $depenses=$model->getAll();$affectations=$model->getActiveAssignments();$categories=$model->getCategories();$totaux=$model->getTotals();
        $toutesCategories=$categoryModel->getAll();
        $message=$_SESSION['expense_message']??null;$messageType=$_SESSION['expense_message_type']??'success';unset($_SESSION['expense_message'],$_SESSION['expense_message_type']);
        $this->view('depenses/index',compact('depenses','affectations','categories','toutesCategories','totaux','message','messageType'));
    }
    private function handlePost(Depense$model,CategorieDepense$categoryModel):void
    {
        $action=$_POST['action']??'';$id=(int)($_POST['id']??0);try{if(str_starts_with($action,'category_')){$this->handleCategory($categoryModel,$action,$id);}elseif($action==='delete'&&$id>0){$model->delete($id);$this->message('Dépense supprimée avec succès.');}else{$d=$this->validatedData();if($action==='create'){$model->create($d);$this->message('Dépense enregistrée avec succès.');}elseif($action==='update'&&$id>0){$model->update($id,$d);$this->message('Dépense modifiée avec succès.');}else throw new RuntimeException('Action invalide.');}}catch(Throwable$e){$message=$e instanceof PDOException&&$e->getCode()==='23000'?'Une catégorie porte déjà ce nom.':$e->getMessage();$this->message($message,'error');}$this->redirect('/depenses');
    }
    private function handleCategory(CategorieDepense$model,string$action,int$id):void
    {
        if($action==='category_toggle'&&$id>0){$model->toggle($id);$this->message('Statut de la catégorie modifié.');return;}
        $nom=trim($_POST['nom']??'');$type=$_POST['type_depense']??'non_participatif';$actif=(int)($_POST['actif']??1);
        if($nom==='')throw new RuntimeException('Le nom de la catégorie est obligatoire.');if(!in_array($type,['participatif','non_participatif'],true))throw new RuntimeException('Le type de dépense est invalide.');
        if($action==='category_create'){$model->create($nom,$type);$this->message('Catégorie créée avec succès.');}elseif($action==='category_update'&&$id>0){$model->update($id,$nom,$type,$actif?1:0);$this->message('Catégorie modifiée avec succès.');}else throw new RuntimeException('Action de catégorie invalide.');
    }
    private function validatedData():array
    {
        $aff=(int)($_POST['affection_vehicule']??0);$cat=(int)($_POST['categorie_depense_id']??0);$date=trim($_POST['date_depense']??'');$libelle=trim($_POST['libelle']??'');$montant=(float)($_POST['montant']??0);$devise=$_POST['devise']??'USD';
        if($aff<=0||$cat<=0||$date===''||$libelle==='')throw new RuntimeException('L’affectation, la catégorie, la date et le libellé sont obligatoires.');if($montant<=0)throw new RuntimeException('Le montant doit être supérieur à zéro.');if(!in_array($devise,['USD','CDF'],true))throw new RuntimeException('La devise est invalide.');
        return['affection_vehicule'=>$aff,'categorie_depense_id'=>$cat,'date_depense'=>$date,'libelle'=>$libelle,'montant'=>$montant,'devise'=>$devise];
    }
    private function message(string$m,string$t='success'):void{$_SESSION['expense_message']=$m;$_SESSION['expense_message_type']=$t;}
}
