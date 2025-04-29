<?php

$pdo = new PDO(
    "mysql:host=localhost;dbname=enset-2025",
    "root",
    ""
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idd'])) {
        $id = $_POST['idd'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $role = $_POST['role'];
        
        $sql = "UPDATE users SET email=?, password=?, role=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $pass, $role, $id]);
        
        header('Location: /');
        exit();
    } else {
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $role = $_POST['role'];
        
        $sql = "INSERT INTO users VALUES(NULL, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $pass, $role]);
        
        header('Location: /');
        exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM users WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    
    header('Location: /');
    exit();
}

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$editMode = false;
$userToEdit = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$id]);
    $userToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userToEdit) {
        $editMode = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users list</title>
    <link rel="stylesheet" href="https://bootswatch.com/5/darkly/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Users List</h1>
        
        <?php if ($editMode): ?>
            <h2>Edit user</h2>
            <form method="post">
                <input type="hidden" name="idd" value="<?= $userToEdit['id'] ?>">
                <input type="text" name="email" placeholder="Email" 
                    value="<?= htmlspecialchars($userToEdit['email']) ?>" class="form-control mb-3">
                <input type="password" value="<?= htmlspecialchars($userToEdit['password']) ?>" 
                    placeholder="Password" name="pass" class="form-control mb-3">
                <select name="role" class="form-select mb-3">
                    <option value="guest" <?= ($userToEdit['role'] == 'guest') ? 'selected' : '' ?>>Guest</option>
                    <option value="author" <?= ($userToEdit['role'] == 'author') ? 'selected' : '' ?>>Author</option>
                    <option value="admin" <?= ($userToEdit['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
                <button class="btn btn-primary">Save</button>
                <a href="/" class="btn btn-secondary">Cancel</a>
            </form>
        <?php else: ?>
            <h2>Add new user</h2>
            <form method="post">
                <input type="text" name="email" placeholder="Email" class="form-control mb-3">
                <input type="password" placeholder="Password" name="pass" class="form-control mb-3">
                <select name="role" class="form-select mb-3">
                    <option value="guest">Guest</option>
                    <option value="author">Author</option>
                    <option value="admin">Admin</option>
                </select>
                <button class="btn btn-success">Add</button>
            </form>
        <?php endif; ?>
        
        <hr>
        <table class="table table-dark">
            <tr>
                <th>ID</th>
                <th>EMAIL</th>
                <th>PASSWORD</th>
                <th>ROLE</th>
                <th colspan="2" class="text-center">Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['password']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td class="text-center">
                        <a onclick="return confirm('Are you sure you want to delete this user?')" 
                           href="?action=delete&id=<?= $user['id'] ?>" class="btn btn-danger">X</a>
                    </td>
                    <td class="text-center">
                        <a href="?action=edit&id=<?= $user['id'] ?>" class="btn btn-primary">E</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
        <hr>
       
    </div>
</body>

</html>
