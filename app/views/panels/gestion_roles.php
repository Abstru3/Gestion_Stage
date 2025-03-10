<?php
require_once 'db_connection.php';

$sql = "SELECT users.id, users.username, users.email, users.role_id, roles.name AS role_name FROM users JOIN roles ON users.role_id = roles.id";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll();

$sql_roles = "SELECT * FROM roles";
$stmt_roles = $pdo->query($sql_roles);
$roles = $stmt_roles->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Gestion des Rôles</title>
    <link rel="stylesheet" href="assets/css/style_admin.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestion des Rôles des Utilisateurs</h2>

        <!-- Tableau des utilisateurs -->
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle actuel</th>
                    <th>Modifier le rôle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role_name']) ?></td>
                        <td>
                            <!-- Formulaire pour changer le rôle -->
                            <form method="POST" action="update_role.php">
                                <select name="role_id" class="form-control">
                                    <?php foreach ($roles as $role) : ?>
                                        <option value="<?= $role['id'] ?>" <?= $role['id'] == $user['role_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($role['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-primary mt-2">Modifier</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
