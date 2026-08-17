<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$controller = new KeywordController();
$projectController = new ProjectController();
$authController = new AuthController();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_POST['action'] ?? $_GET['action'] ?? 'index';

if ($method === 'POST') {
    verifyCsrf($_POST['csrf_token'] ?? null);
}

$publicActions = ['register', 'register_store', 'login', 'login_store'];
if (!in_array($action, $publicActions, true) && !isLoggedIn()) {
    redirect('index.php?action=login');
}

$id = null;
if (isset($_GET['id']) || isset($_POST['id'])) {
    $rawId = $_GET['id'] ?? $_POST['id'];
    if (filter_var($rawId, FILTER_VALIDATE_INT) === false || (int) $rawId <= 0) {
        http_response_code(400);
        echo 'Bad request: invalid id.';
        exit;
    }
    $id = (int) $rawId;
}

switch ($action) {
    case 'register':
        if (isLoggedIn()) {
            redirect('index.php');
        }
        $authController->register();
        break;
    case 'register_store':
        if ($method !== 'POST') {
            redirect('index.php?action=register');
        }
        $authController->store();
        break;
    case 'login':
        if (isLoggedIn()) {
            redirect('index.php');
        }
        $authController->login();
        break;
    case 'login_store':
        if ($method !== 'POST') {
            redirect('index.php?action=login');
        }
        $authController->authenticate();
        break;
    case 'logout':
        if ($method !== 'POST') {
            redirect('index.php');
        }
        $authController->logout();
        break;
    case 'project_index':
        $projectController->index();
        break;
    case 'project_create':
        $projectController->create();
        break;
    case 'project_store':
        if ($method !== 'POST') {
            redirect('index.php?action=project_index');
        }
        $projectController->store();
        break;
    case 'project_edit':
        $projectController->edit($id ?? 0);
        break;
    case 'project_update':
        if ($method !== 'POST') {
            redirect('index.php?action=project_index');
        }
        $projectController->update($id ?? 0);
        break;
    case 'project_destroy':
        if ($method !== 'POST') {
            redirect('index.php?action=project_index');
        }
        $projectController->destroy($id ?? 0);
        break;
    case 'project_switch':
        $projectController->switchProject($id ?? 0);
        break;
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
