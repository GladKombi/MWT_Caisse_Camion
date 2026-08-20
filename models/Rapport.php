<?php
require_once __DIR__ . '/../core/Model.php';

class Rapport extends Model
{
    private function period(string $field,string $start,string $end,int $vehicleId,string $vehicleField='av.vehicule_id'):string
    {
        $sql=" AND $field BETWEEN ".$this->db->quote($start).' AND '.$this->db->quote($end);
        if($vehicleId>0)$sql.=" AND $vehicleField=".$vehicleId;
        return$sql;
    }
    public function getVehicles():array{return$this->db->query("SELECT id,immatriculation,marque,modele FROM vehicules WHERE statut_supprime=0 ORDER BY immatriculation")->fetchAll();}
    public function getSummary(string$start,string$end,int$vehicleId):array
    {
        $out=['USD'=>['recettes'=>0.0,'depenses'=>0.0,'remunerations'=>0.0,'solde'=>0.0],'CDF'=>['recettes'=>0.0,'depenses'=>0.0,'remunerations'=>0.0,'solde'=>0.0]];
        $queries=[
            'recettes'=>"SELECT r.devise,SUM(r.montant) total FROM recettes r JOIN affectations_vehicule av ON av.id=r.affection_vehicule WHERE 1".$this->period('r.date_recette',$start,$end,$vehicleId)." GROUP BY r.devise",
            'depenses'=>"SELECT d.devise,SUM(d.montant) total FROM depenses d JOIN affectations_vehicule av ON av.id=d.affection_vehicule WHERE 1".$this->period('d.date_depense',$start,$end,$vehicleId)." GROUP BY d.devise",
            'remunerations'=>"SELECT rt.devise,SUM(rt.montant) total FROM remunerations_travailleur rt JOIN affectations_vehicule av ON av.id=rt.affectation_vehicule_id WHERE rt.actif=1".$this->period('DATE(rt.created_at)',$start,$end,$vehicleId)." GROUP BY rt.devise"];
        foreach($queries as$key=>$sql)foreach($this->db->query($sql)->fetchAll()as$row)$out[$row['devise']][$key]=(float)$row['total'];
        foreach($out as&$currency)$currency['solde']=$currency['recettes']-$currency['depenses']-$currency['remunerations'];unset($currency);return$out;
    }
    public function getVehiclePerformance(string$start,string$end,int$vehicleId):array
    {
        $filter=$vehicleId>0?' AND v.id='.$vehicleId:'';
        $sql="SELECT v.id,v.immatriculation,v.marque,v.modele,
          COALESCE((SELECT SUM(r.montant) FROM recettes r JOIN affectations_vehicule a ON a.id=r.affection_vehicule WHERE a.vehicule_id=v.id AND r.devise='USD' AND r.date_recette BETWEEN ? AND ?),0) recettes_usd,
          COALESCE((SELECT SUM(d.montant) FROM depenses d JOIN affectations_vehicule a ON a.id=d.affection_vehicule WHERE a.vehicule_id=v.id AND d.devise='USD' AND d.date_depense BETWEEN ? AND ?),0) depenses_usd,
          COALESCE((SELECT SUM(rt.montant) FROM remunerations_travailleur rt JOIN affectations_vehicule a ON a.id=rt.affectation_vehicule_id WHERE a.vehicule_id=v.id AND rt.devise='USD' AND DATE(rt.created_at) BETWEEN ? AND ?),0) remunerations_usd,
          COALESCE((SELECT SUM(r.montant) FROM recettes r JOIN affectations_vehicule a ON a.id=r.affection_vehicule WHERE a.vehicule_id=v.id AND r.devise='CDF' AND r.date_recette BETWEEN ? AND ?),0) recettes_cdf,
          COALESCE((SELECT SUM(d.montant) FROM depenses d JOIN affectations_vehicule a ON a.id=d.affection_vehicule WHERE a.vehicule_id=v.id AND d.devise='CDF' AND d.date_depense BETWEEN ? AND ?),0) depenses_cdf,
          COALESCE((SELECT SUM(rt.montant) FROM remunerations_travailleur rt JOIN affectations_vehicule a ON a.id=rt.affectation_vehicule_id WHERE a.vehicule_id=v.id AND rt.devise='CDF' AND DATE(rt.created_at) BETWEEN ? AND ?),0) remunerations_cdf
          FROM vehicules v WHERE v.statut_supprime=0$filter ORDER BY v.immatriculation";
        $stmt=$this->db->prepare($sql);$params=[];for($i=0;$i<6;$i++)array_push($params,$start,$end);$stmt->execute($params);return$stmt->fetchAll();
    }
    public function getExpenseCategories(string$start,string$end,int$vehicleId):array
    {
        $sql="SELECT c.nom,c.type_depense,d.devise,SUM(d.montant) total,COUNT(*) nombre FROM depenses d JOIN categories_depenses c ON c.id=d.categorie_depense_id JOIN affectations_vehicule av ON av.id=d.affection_vehicule WHERE 1".$this->period('d.date_depense',$start,$end,$vehicleId)." GROUP BY c.id,c.nom,c.type_depense,d.devise ORDER BY total DESC";return$this->db->query($sql)->fetchAll();
    }
    public function getRemunerations(string$start,string$end,int$vehicleId):array
    {
        $sql="SELECT rt.*,t.matricule,TRIM(CONCAT_WS(' ',t.nom,t.postnom,t.prenom)) travailleur,f.nom fonction_nom,v.immatriculation FROM remunerations_travailleur rt JOIN attribution_fonctions af ON af.id=rt.attribution_id JOIN travailleurs t ON t.id=af.travailleur_id JOIN fonctions_travailleur f ON f.id=af.fonction_id JOIN affectations_vehicule av ON av.id=rt.affectation_vehicule_id JOIN vehicules v ON v.id=av.vehicule_id WHERE rt.actif=1".$this->period('DATE(rt.created_at)',$start,$end,$vehicleId)." ORDER BY rt.created_at DESC";return$this->db->query($sql)->fetchAll();
    }
    public function getClosures(string$start,string$end,int$vehicleId):array
    {
        $sql="SELECT cc.*,v.immatriculation,v.marque,v.modele,(SELECT COUNT(*) FROM remunerations_travailleur rt WHERE rt.cloture_id=cc.id) nombre_remunerations FROM clotures_caisse cc JOIN vehicules v ON v.id=cc.vehicule_id WHERE 1".$this->period('DATE(cc.created_at)',$start,$end,$vehicleId,'cc.vehicule_id')." ORDER BY cc.created_at DESC";return$this->db->query($sql)->fetchAll();
    }
}
