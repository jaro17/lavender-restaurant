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
        $menuSection = $xpath->query('//section[@id="menu"]')->item(0);

        if (!$menuSection instanceof \DOMElement) {
            return $html;
        }

        while ($menuSection->firstChild) {
            $menuSection->removeChild($menuSection->firstChild);
        }

        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($this->renderMenuSection());
        $menuSection->appendChild($fragment);

        $result = $document->saveHTML();

        return str_replace('<?xml encoding="UTF-8">', '', (string) $result);
    }

    private function renderMenuSection(): string
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
  <h3 class="font-heading text-xl text-purple-800 border-b-2 border-purple-200 pb-2 mb-4">{$heading}</h3>
  {$itemsHtml}
</div>
HTML;
        }

        return <<<HTML
<div class="max-w-4xl mx-auto px-6">
  <div class="bg-white rounded-2xl shadow-xl border border-purple-100 overflow-hidden print-menu">
    <div class="bg-gradient-to-r from-purple-100 to-purple-50 px-8 py-10 text-center border-b border-purple-100">
      <div class="flex justify-center mb-4">
        <svg class="w-16 h-16 text-purple-400" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C8.5 2 6 4.5 6 7c0 2 1.5 4 3 5.5V22h6v-9.5c1.5-1.5 3-3.5 3-5.5 0-2.5-2.5-5-6-5zm0 2c2.2 0 4 1.8 4 3s-1.8 3-4 5c-2.2-2-4-3.8-4-5s1.8-3 4-3z"></path>
        </svg>
      </div>
      <h2 class="font-heading text-3xl text-gray-800 mb-2">Lavender Restaurant</h2>
      <p class="text-purple-600 font-medium text-lg">Denné menu</p>
      <p class="text-gray-600 mt-2">Telefón: <a class="text-purple-600 hover:underline" href="tel:+421944445221">+421 944 445 221</a></p>
    </div>
    <div class="px-8 py-8 space-y-10">
      {$daysHtml}
    </div>
    <div class="px-8 py-6 bg-purple-50 border-t border-purple-100">
      <p class="text-xs text-gray-500 leading-relaxed"><strong>Alergény:</strong> 1. obilniny obsahujúce lepok, 2. kôrovce, 3. vajcia, 4. ryby, 5. arašidy, 6. sójové zrná (sója), 7. mlieko, 8. škrupinové plody (orechy), piniové, oriešky..., 9. zeler, 10. horčica, 11. sezamové semená (sezam), 12. oxid siričitý a siričitany, 13. vlčí bôb (lupina), 14. mäkkýše.</p>
    </div>
    <div class="px-8 py-6 text-center">
      <button class="print-btn px-8 py-3 bg-purple-600 text-white rounded-full font-medium hover:bg-purple-700 transition-colors inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
        </svg><span>Vytlačiť menu</span>
      </button>
    </div>
  </div>
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
