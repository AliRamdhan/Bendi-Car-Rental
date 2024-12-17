<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Users</title>
</head>

<?php

require 'db/connect.php';
require 'api/User.php';

// Inisialisasi
$database = new Database();
$user = new User($database);

// Handling Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;

    try {
        if ($action == 'create') {
            $user->createUser($_POST['username'], $_POST['email'], $_POST['password'], 1);
        } elseif ($action == 'update' && $id) {
            $user->updateUser($id, $_POST['username'], $_POST['email'], $_POST['password'], 1);
        } elseif ($action == 'delete' && $id) {
            $user->deleteUser($id);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch All Users
$users = $user->getAllUsers();
$editingUser = isset($_GET['edit']) ? $user->getUserById($_GET['edit']) : null;
?>

<body>
    <h1>CRUD Users</h1>

    <!-- Display Error -->
    <?php if (isset($error)) : ?>
    <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $editingUser['id'] ?? ''; ?>">
        <label>
            Username:
            <input type="text" name="username" value="<?php echo $editingUser['username'] ?? ''; ?>" required>
        </label><br>
        <label>
            Email:
            <input type="email" name="email" value="<?php echo $editingUser['email'] ?? ''; ?>" required>
        </label><br>
        <label>
            Password:
            <input type="password" name="password" required>
        </label><br>
        <button type="submit" name="action" value="<?php echo $editingUser ? 'update' : 'create'; ?>">
            <?php echo $editingUser ? 'Update User' : 'Create User'; ?>
        </button>
    </form>

    <h2>All Users</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['username']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['created_at']; ?></td>
                <td>
                    <a href="?edit=<?php echo $user['id']; ?>">Edit</a>
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        <button type="submit" name="action" value="delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>