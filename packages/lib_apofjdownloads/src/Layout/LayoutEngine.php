<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Layout;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * Twig-based layout rendering engine.
 *
 * Resolves layouts through a cascade chain:
 * shortcode param → download-level → category-level → global default → system fallback.
 */
class LayoutEngine
{
    private Environment $twig;

    /**
     * @param  LayoutResolverInterface  $resolver              Resolves layouts from store.
     * @param  string                   $defaultTemplatesPath  Path to system default .html.twig files.
     * @param  ?string                  $templateOverridePath  Optional template override path.
     */
    public function __construct(
        private readonly LayoutResolverInterface $resolver,
        private readonly string $defaultTemplatesPath,
        private readonly ?string $templateOverridePath = null,
    ) {
        $this->twig = new Environment(new ArrayLoader([]), [
            'autoescape' => 'html',
            'strict_variables' => false,
        ]);

        $this->twig->addExtension(new TwigExtension());
    }

    /**
     * Render a layout by type with context variables.
     *
     * @param  string   $type        One of LayoutType constants.
     * @param  array    $context     Template variables.
     * @param  ?string  $alias       Explicit layout alias.
     * @param  ?int     $categoryId  Category scope for fallback resolution.
     *
     * @return string  Rendered HTML.
     */
    public function render(string $type, array $context, ?string $alias = null, ?int $categoryId = null): string
    {
        if (!LayoutType::isValid($type)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid layout type "%s". Valid types: %s', $type, implode(', ', LayoutType::ALL)),
            );
        }

        $layout = $this->resolver->resolve($type, $alias, $categoryId);

        if ($layout !== null) {
            $html = $this->renderTwigSource($layout->bodyTwig, $context);

            if ($layout->css !== '') {
                $html = '<style>' . $layout->css . '</style>' . "\n" . $html;
            }

            return $html;
        }

        // Check template override path
        if ($alias !== null && $this->templateOverridePath !== null) {
            $overridePath = $this->templateOverridePath . '/' . $alias . '.html.twig';

            if (file_exists($overridePath)) {
                return $this->renderTwigSource(file_get_contents($overridePath), $context);
            }
        }

        // Fall back to system default template file
        return $this->renderSystemDefault($type, $context);
    }

    /**
     * Render using the full cascade chain:
     * 1. Explicit alias
     * 2. Download-level override
     * 3. Category-level default
     * 4. Global default
     * 5. Template override file (by type)
     * 6. System fallback template
     *
     * @param  string   $type        One of LayoutType constants.
     * @param  array    $context     Template variables.
     * @param  ?string  $alias       Explicit layout alias.
     * @param  ?int     $downloadId  Download ID for download-level override.
     * @param  ?int     $categoryId  Category scope for fallback resolution.
     *
     * @return string  Rendered HTML.
     */
    public function renderWithCascade(
        string $type,
        array $context,
        ?string $alias = null,
        ?int $downloadId = null,
        ?int $categoryId = null,
    ): string {
        if (!LayoutType::isValid($type)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid layout type "%s". Valid types: %s', $type, implode(', ', LayoutType::ALL)),
            );
        }

        $layout = $this->resolver->resolveWithCascade($type, $alias, $downloadId, $categoryId);

        if ($layout !== null) {
            $html = $this->renderTwigSource($layout->bodyTwig, $context);

            if ($layout->css !== '') {
                $html = '<style>' . $layout->css . '</style>' . "\n" . $html;
            }

            return $html;
        }

        // Check template override path by type
        if ($this->templateOverridePath !== null) {
            $overridePath = $this->templateOverridePath . '/' . $type . '.html.twig';

            if (file_exists($overridePath)) {
                return $this->renderTwigSource(file_get_contents($overridePath), $context);
            }
        }

        // Fall back to system default template file
        return $this->renderSystemDefault($type, $context);
    }

    /**
     * Render a layout by its primary key.
     *
     * @param  int    $layoutId  Layout ID.
     * @param  array  $context   Template variables.
     *
     * @return string  Rendered HTML.
     */
    public function renderById(int $layoutId, array $context): string
    {
        $layout = $this->resolver->resolveById($layoutId);

        if ($layout === null) {
            throw new \RuntimeException(sprintf('Layout with ID %d not found.', $layoutId));
        }

        $html = $this->renderTwigSource($layout->bodyTwig, $context);

        if ($layout->css !== '') {
            $html = '<style>' . $layout->css . '</style>' . "\n" . $html;
        }

        return $html;
    }

    /**
     * Render a system default template file for the given type.
     */
    private function renderSystemDefault(string $type, array $context): string
    {
        $path = $this->defaultTemplatesPath . '/default_' . $type . '.html.twig';

        if (!file_exists($path)) {
            throw new \RuntimeException(
                sprintf('System default template not found: %s', $path),
            );
        }

        return $this->renderTwigSource(file_get_contents($path), $context);
    }

    /**
     * Compile and render a Twig source string with context.
     */
    private function renderTwigSource(string $source, array $context): string
    {
        $template = $this->twig->createTemplate($source);

        return $template->render($context);
    }
}
