<?php
// Connexion à la base de données
$host = 'localhost';
$db   = 'gestion_utilisateurs';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête de jointure
    $sql = "SELECT a.id_admin, u.nom, u.email, a.privileges
            FROM utilisateurs u
            JOIN admin a ON u.id = a.id_utilisateur";

    $stmt = $pdo->query($sql);

    echo "
    <style>
        body { font-family: Arial; background-color: #f2f2f2; padding: 20px; }
        h2 { color: #333; }
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-edit { background-color: #f39c12; }
        .btn-delete { background-color: #e74c3c; }
        .btn-add { background-color: #2ecc71; margin-bottom: 15px; display: inline-block; }
        form {
            margin-bottom: 30px;
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            width: 500px;
        }
        form input, form select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
        }
    </style>

    <h2>Ajouter un administrateur</h2>
    <form method='post' action='ajouter_admin.php'>

        <label>ID utilisateur :</label>
        <input type='number' name='id_utilisateur' required>
        
        <label>Privilèges :</label>
        <select name='privileges'>
            <option value='lecture'>Lecture</option>
            <option value='modification'>Modification</option>
            <option value='admin'>Administrateur</option>
        </select>

        <button class='btn btn-add' type='submit'>Ajouter</button>
    </form>

    <h2>Liste des administrateurs</h2>
    <table>
        <tr><th>Nom</th><th>Email</th><th>Privilèges</th><th>Actions</th></tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['nom']}</td>
                <td>{$row['email']}</td>
                <td>{$row['privileges']}</td>
                <td>
                    <a class='btn btn-edit' href='modifier_admin.php?id={$row['id_admin']}'>Modifier</a>
                    <a class='btn btn-delete' href='supprimer_admin.php?id={$row['id_admin']}' onclick='return confirm(\"Confirmer la suppression ?\")'>Supprimer</a>
                </td>
              </tr>";
    }

    echo "</table>";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
