<?php
require_once __DIR__ . '/../core/Model.php';

class JournalAudit extends Model
{
    public function getFilters():array
    {
        return['actions'=>$this->db->query('SELECT DISTINCT action FROM journal_audit ORDER BY action')->fetchAll(PDO::FETCH_COLUMN),'tables'=>$this->db->query('SELECT DISTINCT table_concernee FROM journal_audit ORDER BY table_concernee')->fetchAll(PDO::FETCH_COLUMN),'utilisateurs'=>$this->db->query("SELECT DISTINCT u.id,u.nom_utilisateur,u.matricule FROM journal_audit j JOIN utilisateurs u ON u.id=j.utilisateur_id ORDER BY u.nom_utilisateur")->fetchAll()];
    }
    public function getAll(array$f):array
    {
        $where=['DATE(j.created_at) BETWEEN ? AND ?'];$params=[$f['debut'],$f['fin']];
        if($f['action']!==''){$where[]='j.action=?';$params[]=$f['action'];}if($f['table']!==''){$where[]='j.table_concernee=?';$params[]=$f['table'];}if($f['utilisateur_id']>0){$where[]='j.utilisateur_id=?';$params[]=$f['utilisateur_id'];}
        $sql="SELECT j.*,COALESCE(u.nom_utilisateur,'Système') utilisateur,COALESCE(u.matricule,'—') matricule,u.profil FROM journal_audit j LEFT JOIN utilisateurs u ON u.id=j.utilisateur_id WHERE ".implode(' AND ',$where).' ORDER BY j.created_at DESC,j.id DESC';$stmt=$this->db->prepare($sql);$stmt->execute($params);return$stmt->fetchAll();
    }
    public function getStats(string$start,string$end):array
    {
        $stmt=$this->db->prepare("SELECT COUNT(*) total,COUNT(DISTINCT utilisateur_id) utilisateurs,SUM(action='CREATE') creations,SUM(action='UPDATE') modifications,SUM(action='DELETE') suppressions,SUM(action IN('LOGIN','LOGOUT')) connexions FROM journal_audit WHERE DATE(created_at) BETWEEN ? AND ?");$stmt->execute([$start,$end]);$r=$stmt->fetch();return array_map('intval',$r?:[]);
    }
}
