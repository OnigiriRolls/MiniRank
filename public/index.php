<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$controller = new KeywordController();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_POST['action'] ?? $_GET['action'] ?? 'index';

$id = null;
if (isset($_GET['id']) || isset($_POST['id'])) {
    $rawId = $_GET['id'] ?? $_POST['id'];
    if (filter_var($rawId, FILTER_VALIDATE_INT) === false || (int) $rawId <= 0) {
        http_response_code(400);
        echo 'Bad request: invalid keyword id.';
        exit;
    }
    $id = (int) $rawId;
}

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
    case 'show':
        $controller->show($id ?? 0);
        break;
    case 'export':
        $controller->exportCsv($id ?? 0);
        break;
    default:
        $controller->index();
}
