<?php

namespace App\Services;

class WastePlanContentParser
{
    public const MIN_WORD_COUNT = 5500;

    public const TOTAL_PAGES = 15;

    public function countWords(string $content): int
    {
        $text = preg_replace('/\[Strana\s+\d+\s+od\s+15\]/i', '', $content);
        $text = preg_replace('/[━─|]/u', ' ', $text ?? '');
        $text = preg_replace('/\s+/u', ' ', trim($text ?? ''));

        if ($text === '') {
            return 0;
        }

        return count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * @return array<int, int>
     */
    public function findPresentPages(string $content): array
    {
        preg_match_all('/\[Strana\s+(\d+)\s+od\s+15\]/i', $content, $matches);

        return array_map('intval', array_unique($matches[1] ?? []));
    }

    /**
     * @return array<int, int>
     */
    public function getMissingPages(string $content): array
    {
        $present = $this->findPresentPages($content);
        $missing = [];

        for ($i = 1; $i <= self::TOTAL_PAGES; $i++) {
            if (! in_array($i, $present, true)) {
                $missing[] = $i;
            }
        }

        return $missing;
    }

    /**
     * @return array<int, array{number: int, title: string, content: string, content_html: string, is_cover: bool, is_toc: bool}>
     */
    public function parsePages(string $content): array
    {
        $parts = preg_split('/\[Strana\s+(\d+)\s+od\s+15\]/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($parts === false || count($parts) < 2) {
            return [[
                'number' => 1,
                'title' => 'Plan upravljanja otpadom',
                'content' => trim($content),
                'content_html' => $this->formatContentHtml(trim($content)),
                'is_cover' => true,
                'is_toc' => false,
            ]];
        }

        $pages = [];
        $intro = trim($parts[0] ?? '');

        if ($intro !== '') {
            $pages[] = [
                'number' => 1,
                'title' => $this->extractPageTitle($intro) ?: 'Naslovna strana',
                'content' => $intro,
                'content_html' => $this->formatContentHtml($intro),
                'is_cover' => true,
                'is_toc' => false,
            ];
        }

        for ($i = 1; $i < count($parts); $i += 2) {
            $pageNum = (int) ($parts[$i] ?? 0);
            $pageContent = trim($parts[$i + 1] ?? '');

            if ($pageContent === '') {
                continue;
            }

            $number = $pageNum ?: count($pages) + 1;

            $pages[] = [
                'number' => $number,
                'title' => $this->extractPageTitle($pageContent) ?: "Strana {$number}",
                'content' => $pageContent,
                'content_html' => $this->formatContentHtml($pageContent),
                'is_cover' => false,
                'is_toc' => $number === 2 && str_contains(mb_strtoupper($pageContent), 'SADRŽAJ'),
            ];
        }

        if ($pages === []) {
            $pages[] = [
                'number' => 1,
                'title' => 'Plan upravljanja otpadom',
                'content' => trim($content),
                'content_html' => $this->formatContentHtml(trim($content)),
                'is_cover' => true,
                'is_toc' => false,
            ];
        }

        return $this->injectTableOfContents($pages);
    }

    /**
     * @param  array<int, array{number: int, title: string, content: string, content_html: string, is_cover: bool, is_toc: bool}>  $pages
     * @return array<int, array{number: int, title: string, content: string, content_html: string, is_cover: bool, is_toc: bool}>
     */
    public function injectTableOfContents(array $pages): array
    {
        $hasToc = collect($pages)->contains(fn (array $p) => $p['is_toc']);

        if ($hasToc || count($pages) < 2) {
            return $pages;
        }

        $tocItems = collect($pages)
            ->reject(fn (array $p) => $p['is_cover'])
            ->map(fn (array $p) => [
                'number' => $p['number'],
                'title' => $p['title'],
            ])
            ->values();

        $tocHtml = '<div class="toc"><h2 class="section-heading">SADRŽAJ</h2><ol class="toc-list">';
        foreach ($tocItems as $item) {
            $tocHtml .= '<li><span class="toc-page">Strana '.$item['number'].'</span> — '.e($item['title']).'</li>';
        }
        $tocHtml .= '</ol></div>';

        array_splice($pages, 1, 0, [[
            'number' => 0,
            'title' => 'Sadržaj',
            'content' => 'SADRŽAJ',
            'content_html' => $tocHtml,
            'is_cover' => false,
            'is_toc' => true,
        ]]);

        return $pages;
    }

    public function formatContentHtml(string $content): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $content) ?: [];
        $html = '';
        $inTable = false;
        $tableRows = [];
        $paragraph = [];

        $flushParagraph = function () use (&$html, &$paragraph): void {
            if ($paragraph === []) {
                return;
            }
            $text = trim(implode(' ', $paragraph));
            if ($text !== '') {
                if ($this->isSectionHeading($text)) {
                    $html .= '<h2 class="section-heading">'.e($text).'</h2>';
                } elseif ($this->isSubHeading($text)) {
                    $html .= '<h3 class="sub-heading">'.e($text).'</h3>';
                } else {
                    $html .= '<p>'.e($text).'</p>';
                }
            }
            $paragraph = [];
        };

        $flushTable = function () use (&$html, &$tableRows, &$inTable): void {
            if ($tableRows === []) {
                $inTable = false;

                return;
            }

            $html .= '<table class="data-table"><thead><tr>';
            $header = array_shift($tableRows);
            foreach ($header as $cell) {
                $html .= '<th>'.e(trim($cell)).'</th>';
            }
            $html .= '</tr></thead><tbody>';

            foreach ($tableRows as $row) {
                if ($this->isTableSeparator($row)) {
                    continue;
                }
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $html .= '<td>'.e(trim($cell)).'</td>';
                }
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
            $tableRows = [];
            $inTable = false;
        };

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                $flushParagraph();
                if ($inTable) {
                    $flushTable();
                }

                continue;
            }

            if ($this->isDecorativeLine($trimmed)) {
                continue;
            }

            if (str_contains($trimmed, '|') && preg_match('/\|.+\|/', $trimmed)) {
                $flushParagraph();
                $inTable = true;
                $tableRows[] = array_map('trim', explode('|', trim($trimmed, '|')));

                continue;
            }

            if ($inTable) {
                $flushTable();
            }

            $paragraph[] = $trimmed;
        }

        $flushParagraph();
        if ($inTable) {
            $flushTable();
        }

        return $html;
    }

    private function extractPageTitle(string $content): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $content) ?: [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || $this->isDecorativeLine($trimmed)) {
                continue;
            }
            if (mb_strlen($trimmed) > 120) {
                continue;
            }
            if (preg_match('/^(STRANA|PLAN UPRAVLJANJA|\[Strana)/iu', $trimmed)) {
                continue;
            }

            return $trimmed;
        }

        return '';
    }

    private function isDecorativeLine(string $line): bool
    {
        return (bool) preg_match('/^[━─=\-\*]+$/u', $line);
    }

    private function isSectionHeading(string $text): bool
    {
        return (bool) preg_match('/^(STRANA\s+\d|PLAN UPRAVLJANJA OTPADOM|UVOD|SADRŽAJ)/iu', $text)
            || (mb_strlen($text) < 80 && mb_strtoupper($text) === $text && mb_strlen($text) > 8);
    }

    private function isSubHeading(string $text): bool
    {
        return (bool) preg_match('/^\d+(\.\d+)?\s+[A-ZČĆŽŠĐ]/u', $text)
            || (bool) preg_match('/^(Korak\s+\d+|SCENARIO\s+\d+)/iu', $text);
    }

    /**
     * @param  array<int, string>  $row
     */
    private function isTableSeparator(array $row): bool
    {
        $joined = implode('', $row);

        return (bool) preg_match('/^[\-\s|]+$/', $joined);
    }
}
