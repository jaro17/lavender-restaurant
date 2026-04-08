<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Service\WeeklyMenu;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HomeController
{
    public function __construct(
        private string $frontendIndexPath,
        private WeeklyMenu $weeklyMenu
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!is_file($this->frontendIndexPath)) {
            $response->getBody()->write('Frontend homepage was not found. Run the frontend build first.');

            return $response->withStatus(500);
        }

        $html = (string) file_get_contents($this->frontendIndexPath);
        $response->getBody()->write($this->injectMenuSection($html));

        return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    private function injectMenuSection(string $html): string
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        @$document->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new \DOMXPath($document);
        $menuContent = $xpath->query('//*[@id="menu-dynamic-content"]')->item(0);

        if (!$menuContent instanceof \DOMElement) {
            return $html;
        }

        while ($menuContent->firstChild) {
            $menuContent->removeChild($menuContent->firstChild);
        }

        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($this->renderMenuContent());
        $menuContent->appendChild($fragment);

        $result = $document->saveHTML();

        return str_replace('<?xml encoding="UTF-8">', '', (string) $result);
    }

    private function renderMenuContent(): string
    {
        $menu = $this->weeklyMenu->get();
        $daysHtml = '';

        foreach ($menu['days'] as $day) {
            $dateLabel = trim((string) $day['date_label']);
            $heading = $this->escape($day['label'] . ($dateLabel !== '' ? ' ' . $dateLabel : ''));
            $itemsHtml = $day['closed']
                ? $this->renderClosedNotice((string) ($day['closed_reason'] ?? ''))
                : $this->renderCategory('Polievka:', $day['soups'], true) . $this->renderCategory('Jedlá:', $day['mains'], false);

            $daysHtml .= <<<HTML
<div class="menu-day">
  <h3 class="font-body text-xl text-purple-800 border-b-2 border-purple-200 pb-2 mb-4">{$heading}</h3>
  {$itemsHtml}
</div>
HTML;
        }

        $weekLabel = trim((string) ($menu['week_label'] ?? ''));
        $weekLabelHtml = $weekLabel !== ''
            ? '<p class="text-center text-sm font-medium text-purple-600 mb-8">' . $this->escape($weekLabel) . '</p>'
            : '';

        return <<<HTML
{$weekLabelHtml}
<div class="space-y-10">
  {$daysHtml}
</div>
HTML;
    }

    private function renderClosedNotice(string $reason): string
    {
        $reason = trim($reason);
        $message = $reason !== '' ? $this->escape($reason) : 'V tento deň sa nevarí.';

        return <<<HTML
<div class="menu-notice">
  {$message}
</div>
HTML;
    }

    private function renderCategory(string $label, array $items, bool $withMargin): string
    {
        $rows = '';

        foreach ($items as $index => $item) {
            $title = trim((string) ($item['title'] ?? ''));

            if ($title === '') {
                continue;
            }

            $price = $this->escape((string) ($item['price'] ?? ''));
            $allergens = trim((string) ($item['allergens'] ?? ''));
            $allergensHtml = $allergens !== '' ? '<span class="text-xs text-gray-500 ml-1">' . $this->escape($allergens) . '</span>' : '';
            $titleHtml = $this->escape(($index + 1) . '. ' . $title);

            $rows .= <<<HTML
<div class="flex justify-between items-start">
  <div class="flex-1"><span class="text-gray-700">{$titleHtml}</span>{$allergensHtml}</div><span class="text-purple-700 font-medium ml-4 whitespace-nowrap">{$price}</span>
</div>
HTML;
        }

        if ($rows === '') {
            $rows = '<p class="text-gray-500">Menu bude doplnené čoskoro.</p>';
        } else {
            $rows = '<div class="space-y-2">' . $rows . '</div>';
        }

        $wrapperClass = $withMargin ? 'mb-6' : '';
        $labelHtml = $this->escape($label);

        return <<<HTML
<div class="{$wrapperClass}">
  <h4 class="text-sm font-semibold text-purple-600 uppercase tracking-wider mb-3">{$labelHtml}</h4>
  {$rows}
</div>
HTML;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
