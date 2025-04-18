<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>


<?php
// Include the model to fetch formation data
include('../../model/afficher formation.php');

// Get the formations from the model
$formations = getFormations(); // Assuming getFormations() is the function to fetch formations from the database
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formation Backoffice</title>
    <style>
        body {
            transition: background-color 0.5s ease;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: rgb(22, 177, 255);
            color: white;
        }

        .delete-btn {
            cursor: pointer;
            padding: 8px;
            margin-right: 5px;
            background-color: #008CBA;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .delete-btn:hover {
            background-color: #005b80;
        }

        tr:nth-child(odd) {
            background-color: #E6F7FF;
        }

        tr:nth-child(even) {
            background-color: #B3E0FF;
        }

        #searchInput {
            padding: 8px;
            margin-bottom: 10px;
        }

        body.dark-mode {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>

<body>
        
    <h2 style="text-align: center;">Formation Backoffice</h2>

    <input type="text" id="searchInput" oninput="filterTable()" placeholder="Search by Title">

    <table id="formationTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Date Creation</th>
                <th>Durée</th>
                <th>Niveau</th>
                <th>Certificat</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dynamically populate the rows from the formations array -->
            <?php if (count($formations) > 0): ?>
                <?php foreach ($formations as $formation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($formation['id']); ?></td>
                        <td><?php echo htmlspecialchars($formation['titre']); ?></td>
                        <td><?php echo htmlspecialchars($formation['description']); ?></td>
                        <td><?php echo htmlspecialchars($formation['date_creation']); ?></td>
                        <td><?php echo htmlspecialchars($formation['duree']); ?></td>
                        <td><?php echo htmlspecialchars($formation['niveau']); ?></td>
                        <td><a href="path_to_certificates/<?php echo htmlspecialchars($formation['certificat']); ?>" target="_blank">View Certificate</a></td>
                        <td>
                            <form action="path_to_controller/delete_formation.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this formation?');">
                                <input type="hidden" name="id" value="<?php echo $formation['id']; ?>">
                                <button class="delete-btn" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No formations found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>


</body>

</html>
