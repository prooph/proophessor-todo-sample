<?php
/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\ProophessorDo\App\View\Helper;

use Zend\View\Helper\AbstractHelper;

class RiotTag extends AbstractHelper
{
    /**
     * @var array
     */
    private $search = ['"', PHP_EOL];

    /**
     * @var array
     */
    private $replace = ['\"', ''];

    public function __invoke(?string $tagName, string $template = null, string $jsFunction = null): string
    {
        if (null === $template) {
            $template = $tagName;
            $tagName = $this->getTagNameFromTemplate($template);
        }

        $template = $this->getView()->partial($template);

        if (null === $jsFunction) {
            $jsFunction = $this->extractJsFunction($template, $tagName);
            $template = $this->removeJsFromTemplate($template, $tagName);
        }

        return 'riot.tag("'.$tagName.'", "' . str_replace($this->search, $this->replace, $template) . '", '.$jsFunction.');';
    }

    private function getTagNameFromTemplate(string $template): string
    {
        $parts = explode('::', $template);

        return array_pop($parts);
    }

    private function extractJsFunction(string $template, string $tagName): string
    {
        preg_match('/<script .*type="text\/javascript"[^>]*>[\s]*(?<func>function.+\});?[\s]*<\/script>/is', $template, $matches);

        if (! $matches['func']) {
            throw new \RuntimeException('Riot tag javascript function could not be found for tag name: ' . $tagName);
        }

        return $matches['func'];
    }

    private function removeJsFromTemplate(string $template, string $tagName): string
    {
        $template = preg_replace('/<script .*type="text\/javascript"[^>]*>.*<\/script>/is', '', $template);

        if (! $template) {
            throw new \RuntimeException('Riot tag template compilation failed for tag: ' . $tagName . ' with error code: ' . preg_last_error());
        }

        return $template;
    }
}
