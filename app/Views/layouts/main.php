<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'My App' ?></title>
</head>
<body>

<?php require_once __DIR__ . '/../partials/header.php'; ?>

<main>
    <?= $content ?>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>