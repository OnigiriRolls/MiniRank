<?php

declare(strict_types=1);

class KeywordController
{
    public function index(): void
    {
        $keywords = Keyword::withStats();
        $this->render('keywords/index', ['keywords' => $keywords]);
    }

    public function create(): void
    {
        $this->renderForm(null, '');
    }

    public function edit(int $id): void
    {
        $keyword = Keyword::find($id);
        if ($keyword === null) {
            $this->render('notfound', [], 404);
            return;
        }
        $this->renderForm($keyword['id'], $keyword['phrase']);
    }

    public function show(int $id): void
    {
        $keyword = Keyword::find($id);
        if ($keyword === null) {
            $this->render('notfound', [], 404);
            return;
        }
        $this->render('keywords/show', [
            'keyword' => $keyword,
            'history' => Position::history($id),
        ]);
    }

    public function exportCsv(int $id): void
    {
        $keyword = Keyword::find($id);
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

    public function store(): void
    {
        $phrase = trim((string) ($_POST['phrase'] ?? ''));
        $error = $this->validate($phrase, null);
        if ($error !== null) {
            $this->renderIndex($phrase, $error, 400);
            return;
        }
        Keyword::create($phrase);
        redirect('index.php');
    }

    public function update(int $id): void
    {
        $keyword = Keyword::find($id);
        if ($keyword === null) {
            $this->render('notfound', [], 404);
            return;
        }
        $phrase = trim((string) ($_POST['phrase'] ?? ''));
        $error = $this->validate($phrase, $id);
        if ($error !== null) {
            $this->renderForm($id, $phrase, $error);
            return;
        }
        Keyword::update($id, $phrase);
        redirect('index.php');
    }

    public function destroy(int $id): void
    {
        Keyword::delete($id);
        redirect('index.php');
    }

    private function validate(string $phrase, ?int $ignoreId): ?string
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
        $existing = Keyword::all();
        foreach ($existing as $kw) {
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

    private function renderForm(?int $id, string $phrase, ?string $error = null): void
    {
        $this->render('keywords/form', [
            'id' => $id,
            'phrase' => $phrase,
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

    private function renderIndex(?string $addPhrase = '', ?string $addError = null, int $status = 200): void
    {
        $keywords = Keyword::withStats();
        $this->render('keywords/index', [
            'keywords' => $keywords,
            'addPhrase' => $addPhrase,
            'addError' => $addError,
        ], $status);
    }
}
