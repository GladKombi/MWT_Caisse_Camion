<?php
require_once __DIR__ . '/../core/Model.php';

class Depense extends Model
{
    public function getAll():array
    {
        $sql="SELECT d.*,c.nom categorie_nom,c.type_depense,av.vehicule_id,v.immatriculation,v.marque,v.modele,
                     t.matricule travailleur_matricule,t.nom,t.postnom,t.prenom,f.nom fonction_nom
              FROM depenses d JOIN categories_depenses c ON c.id=d.categorie_depense_id
              JOIN affectations_vehicule av ON av.id=d.affection_vehicule JOIN vehicules v ON v.id=av.vehicule_id
              JOIN attribution_fonctions af ON af.id=av.attribution_id JOIN travailleurs t ON t.id=af.travailleur_id
              JOIN fonctions_travailleur f ON f.id=af.fonction_id ORDER BY d.date_depense DESC,d.id DESC";
        return $this->db->query($sql)->fetchAll();
    }
    public function getActiveAssignments():array
    {
        $sql="SELECT av.id,v.immatriculation,v.marque,v.modele,t.matricule,t.nom,t.postnom,t.prenom,f.nom fonction_nom
              FROM affectations_vehicule av JOIN vehicules v ON v.id=av.vehicule_id
              JOIN attribution_fonctions af ON af.id=av.attribution_id JOIN travailleurs t ON t.id=af.travailleur_id
              JOIN fonctions_travailleur f ON f.id=af.fonction_id WHERE av.statut_supprime=0
              AND av.date_debut<=CURDATE() AND (av.date_fin IS NULL OR av.date_fin>=CURDATE()) ORDER BY v.immatriculation,t.nom";
        return $this->db->query($sql)->fetchAll();
    }
    public function getCategories():array{return $this->db->query('SELECT id,nom,type_depense FROM categories_depenses WHERE actif=1 ORDER BY nom')->fetchAll();}
    public function getTotals():array{$t=['USD'=>0.0,'CDF'=>0.0];foreach($this->db->query('SELECT devise,SUM(montant) total FROM depenses GROUP BY devise')->fetchAll()as$r)$t[$r['devise']]=(float)$r['total'];return$t;}
    public function create(array$d):void{$this->db->prepare('INSERT INTO depenses(affection_vehicule,categorie_depense_id,date_depense,libelle,montant,devise)VALUES(?,?,?,?,?,?)')->execute([$d['affection_vehicule'],$d['categorie_depense_id'],$d['date_depense'],$d['libelle'],$d['montant'],$d['devise']]);}
    public function update(int$id,array$d):void{$this->db->prepare('UPDATE depenses SET affection_vehicule=?,categorie_depense_id=?,date_depense=?,libelle=?,montant=?,devise=? WHERE id=?')->execute([$d['affection_vehicule'],$d['categorie_depense_id'],$d['date_depense'],$d['libelle'],$d['montant'],$d['devise'],$id]);}
    public function delete(int$id):void{$this->db->prepare('DELETE FROM depenses WHERE id=?')->execute([$id]);}
}
