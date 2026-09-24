<?php
/**
 * core/View.php
 * Renders a view into a layout.
 * The view is captured first (so $pageTitle etc. set inside it
 * are available to the layout when it renders).
 */
class View
{
    public static function render(string $view, array $data = [], string $layout = ''): void
    {
        // Make variables available
        extract($data, EXTR_SKIP);

        $viewFile = __DIR__ . '/../app/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            die('<div style="font-family:monospace;padding:2rem;color:#f87171">
                 <strong>View not found:</strong> ' . htmlspecialchars($view) . '</div>');
        }

        // Always capture the view output first.
        // This lets the view set $pageTitle, $extraCss etc.
        // before the layout's <head> reads them.
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Also pick up any variables the view set via variable extraction
        // (e.g. $pageTitle set at top of view file)
        // We do this by reading the output buffer scope — PHP doesn't
        // propagate variables from require back, so views must set
        // $GLOBALS or we pass pageTitle explicitly. Simplest fix:
        // the view echoes a special marker. Instead we just let
        // layouts use a default title when $pageTitle is unset.

        if ($layout === '') {
            echo $content;
            return;
        }

        $layoutFile = __DIR__ . '/../app/views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            http_response_code(500);
            die('<div style="font-family:monospace;padding:2rem;color:#f87171">
                 <strong>Layout not found:</strong> ' . htmlspecialchars($layout) . '</div>');
        }

        // $content is now available inside the layout
        require $layoutFile;
    }

    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Returns the subfolder base path + given path.
     * base('login') → /moveup/public/login
     * base('css/style.css') → /moveup/public/css/style.css
     */
    public static function base(string $path = ''): string
    {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        return $base . '/' . ltrim($path, '/');
    }

    /**
     * Redirect prepending the subfolder base automatically.
     */
    public static function redirect(string $path): void
    {
        header('Location: ' . self::base($path));
        exit;
    }
}
