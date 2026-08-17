<?php

declare(strict_types=1);

class ProjectController
{
    public function index(): void
    {
        $projects = Project::all();
        foreach ($projects as &$project) {
            $project['keyword_count'] = Project::keywordCount((int) $project['id']);
        }
        unset($project);

        $this->render('projects/index', [
            'projects' => $projects,
            'activeProjectId' => activeProjectId(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm(null, '', null, null);
    }

    public function edit(int $id): void
    {
        $project = Project::find($id);
        if ($project === null) {
            $this->render('notfound', [], 404);
            return;
        }
        $this->renderForm($project['id'], $project['name'], $project['url'], null);
    }

    public function store(): void
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $url = $this->normalizeUrl($_POST['url'] ?? '');

        $error = $this->validate($name, $url, null);
        if ($error !== null) {
            $this->renderForm(null, $name, $url, $error);
            return;
        }
        Project::create($name, $url);
        redirect('index.php?action=project_index');
    }

    public function update(int $id): void
    {
        $project = Project::find($id);
        if ($project === null) {
            $this->render('notfound', [], 404);
            return;
        }
        $name = trim((string) ($_POST['name'] ?? ''));
        $url = $this->normalizeUrl($_POST['url'] ?? '');

        $error = $this->validate($name, $url, $id);
        if ($error !== null) {
            $this->renderForm($id, $name, $url, $error);
            return;
        }
        Project::update($id, $name, $url);
        redirect('index.php?action=project_index');
    }

    public function destroy(int $id): void
    {
        Project::delete($id);
        if (activeProjectId() === $id) {
            setActiveProject(null);
        }
        redirect('index.php?action=project_index');
    }

    public function switchProject(int $id): void
    {
        $project = Project::find($id);
        if ($project === null) {
            $this->render('notfound', [], 404);
            return;
        }
        setActiveProject($id);
        redirect('index.php');
    }

    private function normalizeUrl(mixed $raw): ?string
    {
        $url = trim((string) $raw);
        return $url === '' ? null : $url;
    }

    private function validate(string $name, ?string $url, ?int $ignoreId): ?string
    {
        $name = trim($name);

        if ($name === '') {
            return 'The project name must not be empty.';
        }
        if (mb_strlen($name) > 255) {
            return 'The project name must be at most 255 characters.';
        }
        if (preg_match('/[\x00-\x1F\x7F]/', $name)) {
            return 'The project name must not contain control characters.';
        }
        if ($url !== null) {
            if (mb_strlen($url) > 2048) {
                return 'The URL must be at most 2048 characters.';
            }
            if (preg_match('/[\x00-\x1F\x7F]/', $url)) {
                return 'The URL must not contain control characters.';
            }
        }
        foreach (Project::all() as $project) {
            if (mb_strtolower($project['name']) === mb_strtolower($name) && (int) $project['id'] !== $ignoreId) {
                return 'This project already exists.';
            }
        }
        return null;
    }

    private function renderForm(?int $id, string $name, ?string $url, ?string $error): void
    {
        $this->render('projects/form', [
            'id' => $id,
            'name' => $name,
            'url' => $url,
            'error' => $error,
        ], $error !== null ? 400 : 200);
    }

    private function render(string $view, array $data = [], int $status = 200): void
    {
        http_response_code($status);
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../views/header.php';
        require __DIR__ . '/../../views/' . $view . '.php';
        require __DIR__ . '/../../views/footer.php';
    }
}