<?php
// Static page template helper
function renderStaticPage($title, $content) {
    session_start();
    require __DIR__ . '/../config/config.php';
    require __DIR__ . '/../includes/functions.php';
    $page_title = $title;
    include __DIR__ . '/../includes/header.php';
    echo renderBreadcrumbs([['label' => 'Home', 'url' => '../index.php'], ['label' => $title, 'url' => '']]);
    echo '<div class="card"><h1 style="margin-bottom:1.5rem;">' . e($title) . '</h1>' . $content . '</div>';
    include __DIR__ . '/../includes/footer.php';
}
