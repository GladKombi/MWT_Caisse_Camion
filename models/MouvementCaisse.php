<?php
require_once __DIR__ . '/../core/Model.php';

class MouvementCaisse extends Model
{
    public function getLedger(): array
    {
        $sql="SELECT CONCAT('REC-',r.id) reference,'ENTREE' type_mouvement,r.id source_id,r.date_recette date_operation,
                     r.montant,r.devise,COALESCE(r.description,'Recette') description,v.immatriculation,r.created_at
              FROM recettes r JOIN affectations_vehicule av ON av.id=r.affection_vehicule JOIN vehicules v ON v.id=av.vehicule_id
              UNION ALL
              SELECT CONCAT('DEP-',d.id),'SORTIE',d.id,d.date_depense,d.montant,d.devise,d.libelle,v.immatriculation,d.created_at
              FROM depenses d JOIN affectations_vehicule av ON av.id=d.affection_vehicule JOIN vehicules v ON v.id=av.vehicule_id
              ORDER BY date_operation DESC,created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
    public function getSituation():array
    {
        $s=['USD'=>['entrees'=>0.0,'sorties'=>0.0,'solde'=>0.0],'CDF'=>['entrees'=>0.0,'sorties'=>0.0,'solde'=>0.0]];
        foreach($this->db->query("SELECT devise,SUM(montant) total FROM recettes GROUP BY devise")->fetchAll()as$r)$s[$r['devise']]['entrees']=(float)$r['total'];
        foreach($this->db->query("SELECT devise,SUM(montant) total FROM depenses GROUP BY devise")->fetchAll()as$r)$s[$r['devise']]['sorties']=(float)$r['total'];
        foreach($s as &$currency)$currency['solde']=$currency['entrees']-$currency['sorties'];unset($currency);return$s;
    }
    public function getDailyEvolution(int$days=7):array
    {
        $sql="SELECT x.jour,x.devise,SUM(CASE WHEN x.type='ENTREE' THEN x.montant ELSE 0 END) entrees,SUM(CASE WHEN x.type='SORTIE' THEN x.montant ELSE 0 END) sorties
              FROM(SELECT date_recette jour,devise,montant,'ENTREE' type FROM recettes UNION ALL SELECT date_depense,devise,montant,'SORTIE' FROM depenses)x
              WHERE x.jour>=DATE_SUB(CURDATE(),INTERVAL :days DAY) GROUP BY x.jour,x.devise ORDER BY x.jour";
        $stmt=$this->db->prepare($sql);$stmt->bindValue(':days',$days-1,PDO::PARAM_INT);$stmt->execute();return$stmt->fetchAll();
    }
}
