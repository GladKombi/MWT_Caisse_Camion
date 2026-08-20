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
              UNION ALL
              SELECT CONCAT('REM-',rt.id),'SORTIE',rt.id,DATE(rt.created_at),rt.montant,rt.devise,
                     CONCAT('Rémunération clôture #',rt.cloture_id,' — ',TRIM(CONCAT_WS(' ',t.nom,t.postnom,t.prenom))),v.immatriculation,rt.created_at
              FROM remunerations_travailleur rt
              JOIN affectations_vehicule av ON av.id=rt.affectation_vehicule_id
              JOIN attribution_fonctions af ON af.id=rt.attribution_id
              JOIN travailleurs t ON t.id=af.travailleur_id JOIN vehicules v ON v.id=av.vehicule_id
              ORDER BY date_operation DESC,created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
    public function getSituation():array
    {
        $s=['USD'=>['entrees'=>0.0,'sorties'=>0.0,'solde'=>0.0],'CDF'=>['entrees'=>0.0,'sorties'=>0.0,'solde'=>0.0]];
        foreach($this->db->query("SELECT devise,SUM(montant) total FROM recettes GROUP BY devise")->fetchAll()as$r)$s[$r['devise']]['entrees']=(float)$r['total'];
        foreach($this->db->query("SELECT devise,SUM(montant) total FROM depenses GROUP BY devise")->fetchAll()as$r)$s[$r['devise']]['sorties']=(float)$r['total'];
        foreach($this->db->query("SELECT devise,SUM(montant) total FROM remunerations_travailleur WHERE actif=1 GROUP BY devise")->fetchAll()as$r)$s[$r['devise']]['sorties']+=(float)$r['total'];
        foreach($s as &$currency)$currency['solde']=$currency['entrees']-$currency['sorties'];unset($currency);return$s;
    }
    public function getDailyEvolution(int$days=7):array
    {
        $sql="SELECT x.jour,x.devise,SUM(CASE WHEN x.type='ENTREE' THEN x.montant ELSE 0 END) entrees,SUM(CASE WHEN x.type='SORTIE' THEN x.montant ELSE 0 END) sorties
              FROM(SELECT date_recette jour,devise,montant,'ENTREE' type FROM recettes UNION ALL SELECT date_depense,devise,montant,'SORTIE' FROM depenses UNION ALL SELECT DATE(created_at),devise,montant,'SORTIE' FROM remunerations_travailleur WHERE actif=1)x
              WHERE x.jour>=DATE_SUB(CURDATE(),INTERVAL :days DAY) GROUP BY x.jour,x.devise ORDER BY x.jour";
        $stmt=$this->db->prepare($sql);$stmt->bindValue(':days',$days-1,PDO::PARAM_INT);$stmt->execute();return$stmt->fetchAll();
    }
    public function getClosableVehicles(): array
    {
        $sql="SELECT v.id,v.immatriculation,v.marque,v.modele,x.devise,
                     SUM(CASE WHEN x.type_operation='RECETTE' THEN x.montant ELSE 0 END) recettes,
                     SUM(CASE WHEN x.type_operation='DEPENSE' THEN x.montant ELSE 0 END) depenses,
                     SUM(x.type_operation='RECETTE') nombre_recettes,SUM(x.type_operation='DEPENSE') nombre_depenses
              FROM vehicules v JOIN(
                SELECT av.vehicule_id,r.devise,r.montant,'RECETTE' type_operation FROM recettes r
                JOIN affectations_vehicule av ON av.id=r.affection_vehicule WHERE r.cloturer=0
                UNION ALL
                SELECT av.vehicule_id,d.devise,d.montant,'DEPENSE' FROM depenses d
                JOIN affectations_vehicule av ON av.id=d.affection_vehicule
                JOIN categories_depenses c ON c.id=d.categorie_depense_id
                WHERE d.cloturer=0 AND c.type_depense='participatif'
              )x ON x.vehicule_id=v.id GROUP BY v.id,v.immatriculation,v.marque,v.modele,x.devise ORDER BY v.immatriculation,x.devise";
        $vehicles=[];foreach($this->db->query($sql)->fetchAll()as$row){$id=(int)$row['id'];if(!isset($vehicles[$id]))$vehicles[$id]=['id'=>$id,'immatriculation'=>$row['immatriculation'],'marque'=>$row['marque'],'modele'=>$row['modele'],'USD'=>['recettes'=>0,'depenses'=>0],'CDF'=>['recettes'=>0,'depenses'=>0],'nombre_recettes'=>0,'nombre_depenses'=>0];$vehicles[$id][$row['devise']]=['recettes'=>(float)$row['recettes'],'depenses'=>(float)$row['depenses']];$vehicles[$id]['nombre_recettes']+=(int)$row['nombre_recettes'];$vehicles[$id]['nombre_depenses']+=(int)$row['nombre_depenses'];}return array_values($vehicles);
    }
    public function closeVehicle(int $vehicleId): array
    {
        $this->db->beginTransaction();
        try{
            $r=$this->db->prepare('UPDATE recettes r JOIN affectations_vehicule av ON av.id=r.affection_vehicule SET r.cloturer=1 WHERE av.vehicule_id=? AND r.cloturer=0');$r->execute([$vehicleId]);
            $d=$this->db->prepare("UPDATE depenses d JOIN affectations_vehicule av ON av.id=d.affection_vehicule JOIN categories_depenses c ON c.id=d.categorie_depense_id SET d.cloturer=1 WHERE av.vehicule_id=? AND d.cloturer=0 AND c.type_depense='participatif'");$d->execute([$vehicleId]);
            $result=['recettes'=>$r->rowCount(),'depenses'=>$d->rowCount()];if($result['recettes']+$result['depenses']===0)throw new RuntimeException('Aucune opération participative à clôturer pour ce véhicule.');$this->db->commit();return$result;
        }catch(Throwable$e){if($this->db->inTransaction())$this->db->rollBack();throw$e;}
    }
    public function closeVehicleAndPay(int $vehicleId): array
    {
        $this->db->beginTransaction();
        try{
            $summary=$this->getOpenVehicleSummary($vehicleId);
            if($summary['nombre_recettes']+$summary['nombre_depenses']===0)throw new RuntimeException('Aucune opération participative à clôturer pour ce véhicule.');
            $closing=$this->db->prepare('INSERT INTO clotures_caisse(vehicule_id,total_recettes_usd,total_depenses_usd,total_recettes_cdf,total_depenses_cdf,nombre_recettes,nombre_depenses) VALUES(?,?,?,?,?,?,?)');
            $closing->execute([$vehicleId,$summary['USD']['recettes'],$summary['USD']['depenses'],$summary['CDF']['recettes'],$summary['CDF']['depenses'],$summary['nombre_recettes'],$summary['nombre_depenses']]);
            $closingId=(int)$this->db->lastInsertId();$workers=$this->getActiveVehicleWorkers($vehicleId);
            $pay=$this->db->prepare('INSERT INTO remunerations_travailleur(cloture_id,attribution_id,affectation_vehicule_id,taux_remuneration,montant,devise,date_debut,date_fin,actif) VALUES(?,?,?,?,?,?,CURDATE(),CURDATE(),1)');$remunerations=0;
            foreach($workers as$worker)foreach(['USD','CDF']as$currency){if($summary[$currency]['recettes']<=0&&$summary[$currency]['depenses']<=0)continue;$base=max(0,$summary[$currency]['recettes']-$summary[$currency]['depenses']);$amount=round($base*(float)$worker['taux_remuneration']/100,2);$pay->execute([$closingId,$worker['attribution_id'],$worker['affectation_id'],$worker['taux_remuneration'],$amount,$currency]);$remunerations++;}
            $r=$this->db->prepare('UPDATE recettes r JOIN affectations_vehicule av ON av.id=r.affection_vehicule SET r.cloturer=1 WHERE av.vehicule_id=? AND r.cloturer=0');$r->execute([$vehicleId]);
            $d=$this->db->prepare("UPDATE depenses d JOIN affectations_vehicule av ON av.id=d.affection_vehicule JOIN categories_depenses c ON c.id=d.categorie_depense_id SET d.cloturer=1 WHERE av.vehicule_id=? AND d.cloturer=0 AND c.type_depense='participatif'");$d->execute([$vehicleId]);
            $result=['cloture_id'=>$closingId,'recettes'=>$r->rowCount(),'depenses'=>$d->rowCount(),'travailleurs'=>count($workers),'remunerations'=>$remunerations];$this->db->commit();return$result;
        }catch(Throwable$e){if($this->db->inTransaction())$this->db->rollBack();throw$e;}
    }
    private function getOpenVehicleSummary(int $vehicleId):array
    {
        $summary=['USD'=>['recettes'=>0.0,'depenses'=>0.0],'CDF'=>['recettes'=>0.0,'depenses'=>0.0],'nombre_recettes'=>0,'nombre_depenses'=>0];
        $sql="SELECT x.devise,SUM(CASE WHEN x.type_operation='RECETTE' THEN x.montant ELSE 0 END) recettes,SUM(CASE WHEN x.type_operation='DEPENSE' THEN x.montant ELSE 0 END) depenses,SUM(x.type_operation='RECETTE') nombre_recettes,SUM(x.type_operation='DEPENSE') nombre_depenses FROM(SELECT r.devise,r.montant,'RECETTE' type_operation FROM recettes r JOIN affectations_vehicule av ON av.id=r.affection_vehicule WHERE av.vehicule_id=? AND r.cloturer=0 UNION ALL SELECT d.devise,d.montant,'DEPENSE' FROM depenses d JOIN affectations_vehicule av ON av.id=d.affection_vehicule JOIN categories_depenses c ON c.id=d.categorie_depense_id WHERE av.vehicule_id=? AND d.cloturer=0 AND c.type_depense='participatif')x GROUP BY x.devise";
        $stmt=$this->db->prepare($sql);$stmt->execute([$vehicleId,$vehicleId]);foreach($stmt->fetchAll()as$row){$summary[$row['devise']]=['recettes'=>(float)$row['recettes'],'depenses'=>(float)$row['depenses']];$summary['nombre_recettes']+=(int)$row['nombre_recettes'];$summary['nombre_depenses']+=(int)$row['nombre_depenses'];}return$summary;
    }
    private function getActiveVehicleWorkers(int $vehicleId):array
    {
        $sql="SELECT av.id affectation_id,av.attribution_id,COALESCE(af.taux_remuneration,0) taux_remuneration FROM affectations_vehicule av JOIN attribution_fonctions af ON af.id=av.attribution_id JOIN travailleurs t ON t.id=af.travailleur_id WHERE av.vehicule_id=? AND av.statut_supprime=0 AND av.date_debut<=CURDATE() AND (av.date_fin IS NULL OR av.date_fin>=CURDATE()) AND af.statut='ACTIF' AND t.statut='ACTIF'";$stmt=$this->db->prepare($sql);$stmt->execute([$vehicleId]);return$stmt->fetchAll();
    }
}
