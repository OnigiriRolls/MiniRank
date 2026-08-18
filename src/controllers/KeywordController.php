<?php

declare(strict_types=1);

class KeywordController
{
    public function index(int $projectId): void
    {
        $keywords = Keyword::withStats($projectId);
        $this->render('keywords/index', [
            'keywords' => $keywords,
            'projectId' => $projectId,
            'project' => Project::find($projectId, currentUserId()),
        ]);
    }

    public function create(int $projectId): void
    {
        $this->renderForm(null, '', $projectId);
    }

    public function edit(int $projectId, int $id): void
    {
        $keyword = Keyword::find($id, $projectId);
        if ($keyword === null) {
            $this->render('notfound', [], 404);
            return;
        }
        $this->renderForm($keyword['id'], $keyword['phrase'], $projectId);
    }

    public function show(int $projectId, int $id): void
    {
        $keyword = Keyword::find($id, $projectId);
        if ($keyword === null) {
            $this->render('notfound', [], 404);
            return;
        }
        $this->render('keywords/show', [
            'keyword' => $keyword,
            'history' => Position::history($id),
            'projectId' => $projectId,
        ]);
    }

    public function exportCsv(int $projectId, int $id): void
    {
        $keyword = Keyword::find($id, $projectId);
        if ($keyword === null) {
            $this->render('notfound', [], 404);
            return;
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="positions-' . $this->slugify($keyword['phrase']) . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Date', 'Position'], ';');
        foreach (Position::history($id) as $row) {
            fputcsv($out, [$row['date'], (int) $row['position']], ';');
        }
        fclose($out);
        exit;
    }

    public function store(int $projectId): void
    {
        $phrase = trim((string) ($_POST['phrase'] ?? ''));
        $error = $this->validate($phrase, null, $projectId);
        if ($error !== null) {
            $this->renderIndex($projectId, $phrase, $error, 400);
            return;
        }
        Keyword::create($projectId, $phrase);
        redirect('index.php?action=index&project_id=' . $projectId);
    }

    public function update(int $projectId, int $id): void
    {
        $keyword = Keyword::find($id, $projectId);
        if ($keyword === null) {
            $this->render('notfound', [], 404);
            return;
        }
        $phrase = trim((string) ($_POST['phrase'] ?? ''));
        $error = $this->validate($phrase, $id, $projectId);
        if ($error !== null) {
            $this->renderForm($id, $phrase, $projectId, $error);
            return;
        }
        Keyword::update($id, $projectId, $phrase);
        redirect('index.php?action=index&project_id=' . $projectId);
    }

    public function destroy(int $projectId, int $id): void
    {
        Keyword::delete($id, $projectId);
        redirect('index.php?action=index&project_id=' . $projectId);
    }

    private function validate(string $phrase, ?int $ignoreId, int $projectId): ?string
    {
        $phrase = trim($phrase);

        if ($phrase === '') {
            return 'The keyword must not be empty.';
        }
        if (mb_strlen($phrase) > 255) {
            return 'The keyword must be at most 255 characters.';
        }
        if (preg_match('/[\x00-\x1F\x7F]/', $phrase)) {
            return 'The keyword must not contain control characters.';
        }
        foreach (Keyword::allForProject($projectId) as $kw) {
            if (mb_strtolower($kw['phrase']) === mb_strtolower($phrase) && $kw['id'] !== $ignoreId) {
                return 'This keyword already exists.';
            }
        }
        return null;
    }

    private function slugify(string $phrase): string
    {
        $slug = mb_strtolower(trim($phrase));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug === '' ? 'keyword' : $slug;
    }

    private function renderForm(?int $id, string $phrase, int $projectId, ?string $error = null): void
    {
        $this->render('keywords/form', [
            'id' => $id,
            'phrase' => $phrase,
            'projectId' => $projectId,
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

    private function renderIndex(int $projectId, ?string $addPhrase = '', ?string $addError = null, int $status = 200): void
    {
        $keywords = Keyword::withStats($projectId);
        $this->render('keywords/index', [
            'keywords' => $keywords,
            'projectId' => $projectId,
            'project' => Project::find($projectId, currentUserId()),
            'addPhrase' => $addPhrase,
            'addError' => $addError,
        ], $status);
    }
}