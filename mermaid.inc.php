<?php
// plugin_mermaid.inc.php - Mermaid.js renderer plugin for PukiWiki
// Features:
// - ブロック専用
// - 幅指定（px/%、単位省略可）
// - キャッシュ無効化
// - 空引数チェック
// - Mermaid.js 読み込みは1ページ1回のみ
// 
// 2025-07-18 Version 1.0.0 リリース

// ブロック用
function plugin_mermaid_convert()
{
    return _plugin_mermaid_render(func_get_args());
}

// ブロックキャッシュ制御
function plugin_mermaid_convert_cache()
{
    return FALSE;
}

// 共通レンダリング（ブロック専用）
function _plugin_mermaid_render($args)
{
    $style = '';

    // 幅指定オプション
    if (isset($args[0]) && preg_match('/^\d+(px|%)?$/', trim($args[0]))) {
        $width = trim($args[0]);
        if (preg_match('/^\d+$/', $width)) {
            $width .= 'px';
        }
        $style = ' style="width:' . htmlspecialchars($width) . '"';
        array_shift($args);
    }

    // 中身取得
    $body = isset($args[0]) ? trim($args[0]) : '';

    if ($body === '') {
        return '<span style="color:red">pukiwiki_plugin_mermaid error: empty argument</span>';
    }

    $html = '<div class="mermaid"' . $style . '>' . $body . '</div>';

    global $head_mermaid_loaded;
    if (empty($head_mermaid_loaded)) {
        $script = <<<SCRIPT
<script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
<script>mermaid.initialize({ startOnLoad:true });</script>
SCRIPT;
        $head_mermaid_loaded = true;
        return $script . $html;
    } else {
        return $html;
    }
}
