<?php
/**
 * app/controllers/ObjectifsController.php
 */
class ObjectifsController
{
    public function add(): void
    {
        Auth::requireLogin();
        $userId = (int)Auth::user()['id'];

        $titre    = trim($_POST['titre']           ?? '');
        $type     = trim($_POST['type']            ?? 'autre');
        $depart   = (float)($_POST['valeur_depart']   ?? 0);
        $cible    = (float)($_POST['valeur_cible']    ?? 0);
        $actuelle = (float)($_POST['valeur_actuelle'] ?? $depart);
        $unite    = trim($_POST['unite']           ?? '');
        $echeance = trim($_POST['date_echeance']   ?? '');

        if ($titre === '' || $cible == 0) {
            View::json(['success' => false, 'message' => 'Titre et valeur cible requis.'], 422);
            return;
        }

        $allowed = ['poids', 'seances', 'calories', 'autre'];
        if (!in_array($type, $allowed)) $type = 'autre';

        $echeanceVal = ($echeance !== '' && strtotime($echeance)) ? $echeance : null;

        $db   = Database::get();
        $stmt = $db->prepare(
            'INSERT INTO objectifs
             (user_id, titre, type, valeur_depart, valeur_cible, valeur_actuelle, unite, date_echeance, statut)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, "en_cours")'
        );
        $stmt->bind_param('issdddss',
            $userId, $titre, $type,
            $depart, $cible, $actuelle,
            $unite, $echeanceVal
        );
        $stmt->execute();
        $newId = (int)$db->insert_id;
        $stmt->close();

        View::json(['success' => true, 'id' => $newId, 'message' => 'Objectif créé !']);
    }

    public function delete(): void
    {
        Auth::requireLogin();
        $userId = (int)Auth::user()['id'];
        $id     = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            View::json(['success' => false, 'message' => 'ID invalide.'], 422);
            return;
        }

        $db   = Database::get();
        $stmt = $db->prepare('DELETE FROM objectifs WHERE id=? AND user_id=?');
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();

        View::json(['success' => $ok, 'message' => $ok ? 'Objectif supprimé.' : 'Introuvable.']);
    }

    public function updateProgress(): void
    {
        Auth::requireLogin();
        $userId   = (int)Auth::user()['id'];
        $id       = (int)($_POST['id']       ?? 0);
        $actuelle = (float)($_POST['valeur'] ?? 0);

        if ($id <= 0) {
            View::json(['success' => false, 'message' => 'ID invalide.'], 422);
            return;
        }

        $db = Database::get();

        // Check if target reached
        $stmt = $db->prepare('SELECT valeur_cible, valeur_depart FROM objectifs WHERE id=? AND user_id=?');
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            View::json(['success' => false, 'message' => 'Objectif introuvable.']);
            return;
        }

        $statut = 'en_cours';
        $cible  = (float)$row['valeur_cible'];
        $depart = (float)$row['valeur_depart'];
        if ($depart < $cible && $actuelle >= $cible) $statut = 'atteint';
        if ($depart > $cible && $actuelle <= $cible) $statut = 'atteint';

        $stmt2 = $db->prepare('UPDATE objectifs SET valeur_actuelle=?, statut=? WHERE id=? AND user_id=?');
        $stmt2->bind_param('dsii', $actuelle, $statut, $id, $userId);
        $stmt2->execute();
        $stmt2->close();

        View::json(['success' => true, 'statut' => $statut]);
    }
}
