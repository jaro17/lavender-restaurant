<?php

declare(strict_types=1);

namespace App\Service;

use PDO;

final class WeeklyMenu
{
    private const DAY_KEYS = [
        'pondelok' => 'Pondelok',
        'utorok' => 'Utorok',
        'streda' => 'Streda',
        'stvrtok' => 'Štvrtok',
        'piatok' => 'Piatok',
    ];

    public function __construct(private PDO $pdo)
    {
    }

    public function get(): array
    {
        $statement = $this->pdo->query('SELECT week_label, menu_text, updated_at FROM weekly_menu WHERE id = 1');
        $menu = $statement->fetch();

        $menu = $menu ?: [
            'week_label' => '',
            'menu_text' => '',
            'updated_at' => null,
        ];

        return [
            'week_label' => (string) $menu['week_label'],
            'menu_text' => (string) $menu['menu_text'],
            'updated_at' => $menu['updated_at'],
            'days' => $this->decodeDays((string) $menu['menu_text']),
        ];
    }

    public function save(array $days): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE weekly_menu SET menu_text = :menu_text, updated_at = CURRENT_TIMESTAMP WHERE id = 1'
        );
        $statement->execute([
            'menu_text' => json_encode($this->normalizeDays($days), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        ]);
    }

    public function normalizeDays(array $input): array
    {
        $days = [];

        foreach (self::DAY_KEYS as $dayKey => $dayLabel) {
            $day = (array) ($input[$dayKey] ?? []);
            $closed = $this->toBoolean($day['closed'] ?? false);

            $days[$dayKey] = [
                'label' => $dayLabel,
                'date_label' => trim((string) ($day['date_label'] ?? '')),
                'closed' => $closed,
                'closed_reason' => trim((string) ($day['closed_reason'] ?? '')),
                'soups' => $this->normalizeItems((array) ($day['soups'] ?? []), 2),
                'mains' => $this->normalizeItems((array) ($day['mains'] ?? []), 3),
            ];
        }

        return $days;
    }

    private function decodeDays(string $menuText): array
    {
        if ($menuText !== '') {
            $decoded = json_decode($menuText, true);

            if (is_array($decoded)) {
                return $this->normalizeDays($decoded);
            }
        }

        return $this->defaultDays();
    }

    private function normalizeItems(array $items, int $count): array
    {
        $normalized = [];

        for ($index = 0; $index < $count; $index++) {
            $item = (array) ($items[$index] ?? []);

            $normalized[] = [
                'title' => trim((string) ($item['title'] ?? '')),
                'allergens' => trim((string) ($item['allergens'] ?? '')),
                'price' => trim((string) ($item['price'] ?? '')),
            ];
        }

        return $normalized;
    }

    private function defaultDays(): array
    {
        $days = [];

        foreach (self::DAY_KEYS as $dayKey => $dayLabel) {
            $days[$dayKey] = [
                'label' => $dayLabel,
                'date_label' => '',
                'closed' => false,
                'closed_reason' => '',
                'soups' => $this->normalizeItems([], 2),
                'mains' => $this->normalizeItems([], 3),
            ];
        }

        return $days;
    }

    private function toBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'on', 'yes'], true);
        }

        if (is_int($value)) {
            return $value === 1;
        }

        return false;
    }
}
