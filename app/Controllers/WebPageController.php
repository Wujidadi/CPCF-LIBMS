<?php

namespace App\Controllers;

use App\Controller;

/**
 * 網頁控制器
 */
abstract class WebPageController extends Controller
{
    /**
     * 本類別名稱
     *
     * @var string
     */
    protected $_className;

    /**
     * 單一物件實體
     *
     * @var self|null
     */
    protected static $_uniqueInstance;

    /**
     * 取得單一實體
     *
     * @return self
     */
    abstract public static function getInstance();

    /**
     * 組建輸出於 HTML 中的 JavaScript 標籤（`<script>`）語法
     *
     * @param  array   $scripts  JavaScript 路徑陣列
     * @return string
     */
    protected function _buildScriptHTML(array $scripts): string
    {
        $html = '';

        foreach ($scripts as $script)
        {
            $html .=  <<<HTML
            <script src="{$script}"></script>
            
            HTML;
        }

        return $html;
    }
}
