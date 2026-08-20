<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/JournalAudit.php';

class AuditController extends Controller
{
    public function index(): void
    {
        if(($_SESSION['user']['role']??'')!=='ADMIN'){$this->redirect('/dashboard');return;}
        $debut=$_GET['debut']??date('Y-m-01');$fin=$_GET['fin']??date('Y-m-d');if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$debut))$debut=date('Y-m-01');if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$fin))$fin=date('Y-m-d');if($debut>$fin)[$debut,$fin]=[$fin,$debut];
        $filtres=['debut'=>$debut,'fin'=>$fin,'action'=>trim($_GET['action']??''),'table'=>trim($_GET['table']??''),'utilisateur_id'=>max(0,(int)($_GET['utilisateur_id']??0))];$model=new JournalAudit();$options=$model->getFilters();$journaux=$model->getAll($filtres);$stats=$model->getStats($debut,$fin);$this->view('audit/index',compact('filtres','options','journaux','stats'));
    }
}
