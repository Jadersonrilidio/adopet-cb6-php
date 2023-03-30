<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Http\Core;

class View
{
    /**
     * 
     */
    public function renderlayout(string $title = 'ScubaPHP', string $content = ''): string
    {
        $layout = file_get_contents(filename: LAYOUT_PATH . 'layout.html');

        $this->loadContent($layout, [
            'title' => $title,
            'content' => $content
        ]);

        return $layout;
    }

    /**
     * 
     */
    public function renderView(string $template, array $content = []): string
    {
        $page = file_get_contents(filename: VIEW_PATH . $template . '.html');

        $this->loadContent($page, $content);

        return $page;
    }

    /**
     * 
     */
    public function renderComponent(string $template, array $content = []): string
    {
        $component = file_get_contents(filename: COMPONENT_PATH . $template . '.html');

        $this->loadContent($component, $content);

        return $component;
    }

    /**
     * 
     */
    public function renderStatusComponent(string $statusClass = '', string $statusMessage = ''): string
    {
        return (!empty($statusClass) and !empty($statusMessage))
            ? $this->renderComponent(
                template: 'status',
                content: array(
                    'status-class' => $statusClass,
                    'status-message' => $statusMessage
                )
            ) : '';
    }

    /**
     * 
     */
    public function renderErrorMessageComponent(array $errorMessages = []): string
    {
        return !empty($errorMessages)
            ? $this->renderComponent(
                template: 'input_message',
                content: array(
                    'error-message' => implode(' ', $errorMessages)
                )
            ) : '';
    }

    /**
     * 
     */
    private function loadContent(string &$page, array $content): void
    {
        foreach ($content as $placeholder => $value) {
            $page = str_replace(
                search: '{{' . $placeholder . '}}',
                replace: $value,
                subject: $page
            );
        }
    }
}
