<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageCustomizerService
{
    private const TEMPLATE_DIRECTORY = 'page-customizer/templates';
    private const DRAFT_DIRECTORY = 'page-customizer/drafts';
    private const REVISION_DIRECTORY = 'page-customizer/revisions';

    public function templates(): array
    {
        $disk = Storage::disk('local');
        $files = collect(File::glob(resource_path(self::TEMPLATE_DIRECTORY.'/*.json')) ?: [])
            ->map(fn (string $file) => self::TEMPLATE_DIRECTORY.'/'.basename($file))
            ->merge($disk->files(self::TEMPLATE_DIRECTORY))
            ->merge(collect(File::glob(storage_path('app/'.self::TEMPLATE_DIRECTORY.'/*.json')) ?: [])
                ->map(fn (string $file) => self::TEMPLATE_DIRECTORY.'/'.basename($file)))
            ->unique()
            ->filter(fn (string $path) => Str::endsWith($path, '.json'))
            ->sort()
            ->values();

        return $files->map(function (string $path) {
            $data = $this->readTemplate($path) ?: [];

            return [
                'path' => $path,
                'name' => $data['name'] ?? Str::headline(basename($path, '.json')),
                'title' => $data['title'] ?? null,
            ];
        })->all();
    }

    public function templateOptions(): array
    {
        return collect($this->templates())->pluck('name', 'path')->all();
    }

    public function readTemplate(?string $path): ?array
    {
        if (! $path || ! $this->isAllowedTemplatePath($path)) {
            return null;
        }

        $bundled = $this->readBundledTemplate($path);
        $disk = Storage::disk('local');
        if ($disk->exists($path)) {
            $data = json_decode($disk->get($path), true);

            return is_array($data) ? $this->mergeBundledDataDefaults($data, $bundled) : null;
        }

        // Continue honoring customizations saved before templates became bundled resources.
        $legacyOverride = storage_path('app/'.$path);
        if (is_file($legacyOverride)) {
            $data = json_decode(File::get($legacyOverride), true);

            return is_array($data) ? $this->mergeBundledDataDefaults($data, $bundled) : null;
        }

        return $bundled;
    }

    public function writeTemplate(string $path, array $payload): void
    {
        abort_unless($this->isAllowedTemplatePath($path), 422);

        $this->writeJson($path, $this->normalizeTemplate($payload));
    }

    public function readDraftTemplate(?string $path): ?array
    {
        if (! $path || ! $this->isAllowedTemplatePath($path)) {
            return null;
        }

        $draftPath = $this->draftPath($path);
        $disk = Storage::disk('local');
        if (! $disk->exists($draftPath)) {
            return $this->readTemplate($path);
        }

        $draft = json_decode($disk->get($draftPath), true);

        return is_array($draft)
            ? $this->mergeBundledDataDefaults($draft, $this->readBundledTemplate($path))
            : $this->readTemplate($path);
    }

    public function writeDraft(string $path, array $payload): void
    {
        abort_unless($this->isAllowedTemplatePath($path), 422);

        $this->writeJson($this->draftPath($path), $this->normalizeTemplate($payload));
    }

    public function hasDraft(string $path): bool
    {
        return $this->isAllowedTemplatePath($path)
            && Storage::disk('local')->exists($this->draftPath($path));
    }

    public function discardDraft(string $path): void
    {
        abort_unless($this->isAllowedTemplatePath($path), 422);

        Storage::disk('local')->delete($this->draftPath($path));
    }

    public function publishDraft(string $path): array
    {
        abort_unless($this->isAllowedTemplatePath($path), 422);
        abort_unless($this->hasDraft($path), 422, 'There is no saved draft to publish.');

        $published = $this->readTemplate($path);
        $revision = $published ? $this->createRevision($path, $published) : null;
        $draft = $this->readDraftTemplate($path);
        abort_unless($draft, 422, 'The saved draft is invalid.');

        $this->writeTemplate($path, $draft);
        $this->discardDraft($path);

        return [
            'template' => $this->readTemplate($path),
            'revision' => $revision,
        ];
    }

    public function revisions(string $path): array
    {
        if (! $this->isAllowedTemplatePath($path)) {
            return [];
        }

        $disk = Storage::disk('local');

        return collect($disk->files($this->revisionDirectory($path)))
            ->filter(fn (string $file) => Str::endsWith($file, '.json'))
            ->sortDesc()
            ->map(fn (string $file) => [
                'id' => basename($file, '.json'),
                'created_at' => date(DATE_ATOM, $disk->lastModified($file)),
            ])
            ->values()
            ->all();
    }

    public function currentPublishedVersion(string $path): ?array
    {
        if (! $this->isAllowedTemplatePath($path) || ! $this->readTemplate($path)) {
            return null;
        }

        $disk = Storage::disk('local');
        if ($disk->exists($path)) {
            $timestamp = $disk->lastModified($path);
        } else {
            $bundledPath = resource_path($path);
            $legacyPath = storage_path('app/'.$path);
            $timestamp = is_file($legacyPath)
                ? File::lastModified($legacyPath)
                : (is_file($bundledPath) ? File::lastModified($bundledPath) : now()->timestamp);
        }

        return [
            'created_at' => date(DATE_ATOM, $timestamp),
            'current' => true,
        ];
    }

    public function currentDraftVersion(string $path): ?array
    {
        if (! $this->hasDraft($path)) {
            return null;
        }

        $disk = Storage::disk('local');
        $draftPath = $this->draftPath($path);

        return [
            'created_at' => date(DATE_ATOM, $disk->lastModified($draftPath)),
            'draft' => true,
        ];
    }

    public function restoreRevisionAsDraft(string $path, string $revisionId): array
    {
        abort_unless($this->isAllowedTemplatePath($path), 422);
        abort_unless((bool) preg_match('/^[a-z0-9_-]+$/i', $revisionId), 422, 'Invalid revision.');

        $file = $this->revisionDirectory($path).'/'.$revisionId.'.json';
        $disk = Storage::disk('local');
        abort_unless($disk->exists($file), 404, 'Revision not found.');

        $revision = json_decode($disk->get($file), true);
        abort_unless(is_array($revision), 422, 'Revision is invalid.');

        $this->writeDraft($path, $revision);

        return $this->readDraftTemplate($path) ?? [];
    }

    private function normalizeTemplate(array $payload): array
    {

        $page = [
            'name' => $payload['name'] ?? 'Custom page',
            'title' => $payload['title'] ?? '',
            'template' => $payload['template'] ?? 'page',
            'order' => array_values(array_filter($payload['order'] ?? [], 'is_string')),
            'sections' => [],
        ];

        foreach (($payload['sections'] ?? []) as $id => $section) {
            if (! is_string($id) || ! preg_match('/^[a-z0-9_-]+$/i', $id) || ! is_array($section)) {
                continue;
            }

            $page['sections'][$id] = [
                'type' => $this->sanitizeHandle($section['type'] ?? $id),
                'disabled' => ! empty($section['disabled']),
                'data' => is_array($section['data'] ?? null) ? $section['data'] : [],
            ];
        }

        return $page;
    }

    public function sectionSchemas(): array
    {
        $schemas = [];
        foreach (File::glob(resource_path('page-customizer/sections/schema/*.json')) ?: [] as $file) {
            $schema = json_decode(File::get($file), true);
            if (is_array($schema) && isset($schema['id'], $schema['schema'])) {
                $schemas[] = $schema;
            }
        }

        usort($schemas, fn (array $left, array $right) => strcmp($left['id'], $right['id']));

        return ['schema_path' => 'resources/page-customizer/sections/schema', 'sections' => $schemas];
    }

    public function render(?string $path, bool $fullPage = false): string
    {
        $template = $this->readTemplate($path);
        if (! $template) {
            return '';
        }

        return $this->renderTemplate($template, $fullPage);
    }

    public function renderTemplate(array $template, bool $fullPage = false): string
    {

        $allSections = $this->allSectionData($template);
        $html = '';

        foreach (($template['order'] ?? []) as $sectionId) {
            if (! $fullPage && in_array($sectionId, ['header', 'footer'], true)) {
                continue;
            }

            $html .= $this->renderSection($template, (string) $sectionId, $allSections);
        }

        return $html;
    }

    private function writeJson(string $path, array $template): void
    {
        Storage::disk('local')->put(
            $path,
            json_encode($template, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL,
        );
    }

    private function createRevision(string $path, array $template): string
    {
        $id = now()->format('Ymd-His-u').'-'.Str::lower(Str::random(4));
        $this->writeJson($this->revisionDirectory($path).'/'.$id.'.json', $this->normalizeTemplate($template));

        return $id;
    }

    private function draftPath(string $path): string
    {
        return self::DRAFT_DIRECTORY.'/'.basename($path);
    }

    private function revisionDirectory(string $path): string
    {
        return self::REVISION_DIRECTORY.'/'.basename($path, '.json');
    }

    private function renderSection(array $template, string $sectionId, array $allSections): string
    {
        $section = $template['sections'][$sectionId] ?? null;
        if (! is_array($section) || ! empty($section['disabled'])) {
            return '';
        }

        $type = $this->sanitizeHandle($section['type'] ?? $sectionId);
        $file = resource_path("page-customizer/sections/{$type}.php");
        if (! is_file($file)) {
            return '';
        }

        $data = $section['data'] ?? [];
        require_once app_path('Support/page_customizer_helpers.php');

        ob_start();
        include $file;
        $html = (string) ob_get_clean();
        $sectionAttribute = ' data-customizer-section-id="'.htmlspecialchars($sectionId, ENT_QUOTES, 'UTF-8').'"';

        return preg_replace_callback(
            '/^(\s*<[a-z][^>]*)(>)/i',
            fn (array $matches) => $matches[1].$sectionAttribute.$matches[2],
            $html,
            1,
        ) ?? $html;
    }

    private function allSectionData(array $template): array
    {
        $data = [];
        foreach (($template['sections'] ?? []) as $id => $section) {
            if (is_array($section)) {
                $data[$id] = $section['data'] ?? [];
            }
        }

        return $data;
    }

    private function readBundledTemplate(string $path): ?array
    {
        $file = resource_path($path);
        $data = is_file($file) ? json_decode(File::get($file), true) : null;

        return is_array($data) ? $data : null;
    }

    private function mergeBundledDataDefaults(array $saved, ?array $bundled): array
    {
        if (! $bundled || ! is_array($saved['sections'] ?? null)) {
            return $saved;
        }

        foreach ($saved['sections'] as $id => &$section) {
            $defaultSection = $bundled['sections'][$id] ?? null;
            if (! is_array($section) || ! is_array($defaultSection)) {
                continue;
            }

            if (($section['type'] ?? $id) !== ($defaultSection['type'] ?? $id)) {
                continue;
            }

            $savedData = is_array($section['data'] ?? null) ? $section['data'] : [];
            $defaultData = is_array($defaultSection['data'] ?? null) ? $defaultSection['data'] : [];
            $section['data'] = $this->mergeMissingValues($savedData, $defaultData);
        }
        unset($section);

        return $saved;
    }

    private function mergeMissingValues(array $saved, array $defaults): array
    {
        $savedIsList = array_is_list($saved);
        $defaultsAreList = array_is_list($defaults);

        if ($savedIsList && $defaultsAreList) {
            foreach ($saved as $index => &$item) {
                if (! is_array($item)) {
                    continue;
                }

                $defaultItem = $this->matchingDefaultItem($item, $defaults, $index);
                if (is_array($defaultItem)) {
                    $item = $this->mergeMissingValues($item, $defaultItem);
                }
            }
            unset($item);

            return $saved;
        }

        if ($savedIsList !== $defaultsAreList) {
            return $saved === [] && ! $defaultsAreList ? $defaults : $saved;
        }

        foreach ($defaults as $key => $default) {
            if (! array_key_exists($key, $saved)) {
                $saved[$key] = $default;
                continue;
            }

            if (is_array($saved[$key]) && is_array($default)) {
                $saved[$key] = $this->mergeMissingValues($saved[$key], $default);
            }
        }

        return $saved;
    }

    private function matchingDefaultItem(array $savedItem, array $defaults, int $index): ?array
    {
        foreach (['id', 'number', 'handle', 'title'] as $identityKey) {
            if (! isset($savedItem[$identityKey]) || ! is_scalar($savedItem[$identityKey])) {
                continue;
            }

            foreach ($defaults as $defaultItem) {
                if (is_array($defaultItem)
                    && isset($defaultItem[$identityKey])
                    && (string) $defaultItem[$identityKey] === (string) $savedItem[$identityKey]) {
                    return $defaultItem;
                }
            }
        }

        return is_array($defaults[$index] ?? null) ? $defaults[$index] : null;
    }

    private function isAllowedTemplatePath(string $path): bool
    {
        return str_starts_with($path, self::TEMPLATE_DIRECTORY.'/')
            && Str::endsWith($path, '.json')
            && ! str_contains($path, '..');
    }

    private function sanitizeHandle(string $value): string
    {
        return preg_match('/^[a-z0-9_-]+$/i', $value) ? $value : '';
    }
}
