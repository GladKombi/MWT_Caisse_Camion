<?php
require_once __DIR__ . '/../core/Model.php';

class Recette extends Model
{
    public function getAll(): array
    {
        $sql="SELECT r.*,av.vehicule_id,v.immatriculation,v.marque,v.modele,
                     t.matricule travailleur_matricule,t.nom,t.postnom,t.prenom,f.nom fonction_nom
              FROM recettes r JOIN affectations_vehicule av ON av.id=r.affection_vehicule
              JOIN vehicules v ON v.id=av.vehicule_id
              JOIN attribution_fonctions af ON af.id=av.attribution_id
              JOIN travailleurs t ON t.id=af.travailleur_id
              JOIN fonctions_travailleur f ON f.id=af.fonction_id
              ORDER BY r.date_recette DESC,r.id DESC";
        return $this->db->query($sql)->fetchAll();
    }
    public function getActiveAssignments():array
    {
        $sql="SELECT av.id,v.immatriculation,v.marque,v.modele,t.matricule,t.nom,t.postnom,t.prenom,f.nom fonction_nom
              FROM affectations_vehicule av JOIN vehicules v ON v.id=av.vehicule_id
              JOIN attribution_fonctions af ON af.id=av.attribution_id JOIN travailleurs t ON t.id=af.travailleur_id
              JOIN fonctions_travailleur f ON f.id=af.fonction_id
              WHERE av.statut_supprime=0 AND av.date_debut<=CURDATE() AND (av.date_fin IS NULL OR av.date_fin>=CURDATE())
              ORDER BY v.immatriculation,t.nom";
        return $this->db->query($sql)->fetchAll();
    }
    public function getTotals():array
    {
        $totals=['USD'=>0.0,'CDF'=>0.0];foreach($this->db->query('SELECT devise,SUM(montant) total FROM recettes GROUP BY devise')->fetchAll() as $r)$totals[$r['devise']]=(float)$r['total'];return $totals;
    }
    public function create(array $d):void{$this->db->prepare('INSERT INTO recettes(affection_vehicule,date_recette,montant,devise,description)VALUES(?,?,?,?,?)')->execute([$d['affection_vehicule'],$d['date_recette'],$d['montant'],$d['devise'],$d['description']?:null]);}
    public function update(int $id,array $d):void
    {
        if (!$this->isOpen($id)) throw new RuntimeException('Seule une recette non clôturée peut être modifiée.');
        $s=$this->db->prepare('UPDATE recettes SET affection_vehicule=?,date_recette=?,montant=?,devise=?,description=? WHERE id=? AND cloturer=0');
        $s->execute([$d['affection_vehicule'],$d['date_recette'],$d['montant'],$d['devise'],$d['description']?:null,$id]);
    }
    public function close(int $id):void{$this->db->prepare('UPDATE recettes SET cloturer=1 WHERE id=? AND cloturer=0')->execute([$id]);}
    public function delete(int $id):void
    {
        $s=$this->db->prepare('DELETE FROM recettes WHERE id=? AND cloturer=0');$s->execute([$id]);if(!$s->rowCount())throw new RuntimeException('Une recette clôturée ne peut pas être supprimée.');
    }
    private function isOpen(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM recettes WHERE id = ? AND cloturer = 0');
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn() === 1;
    }
}
