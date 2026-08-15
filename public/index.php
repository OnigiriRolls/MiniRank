<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$controller = new KeywordController();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_POST['action'] ?? $_GET['action'] ?? 'index';
$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : null);

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'store':
        if ($method !== 'POST') {
            redirect('index.php');
        }
        $controller->store();
        break;
    case 'edit':
        $controller->edit($id ?? 0);
        break;
    case 'update':
        if ($method !== 'POST') {
            redirect('index.php');
        }
        $controller->update($id ?? 0);
        break;
    case 'destroy':
        if ($method !== 'POST') {
            redirect('index.php');
        }
        $controller->destroy($id ?? 0);
        break;
    case 'refresh':
        if ($method !== 'POST') {
            redirect('index.php');
        }
        $refresh = new RefreshController();
        $refresh->refresh();
        break;
    default:
        $controller->index();
}
