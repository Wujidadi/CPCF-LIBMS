<?php

namespace App\Controllers\Web;

use App\Controllers\WebPageController;
use App\Constant;

/**
 * 主功能頁面控制器
 */
class MainController extends WebPageController
{
    protected static $_uniqueInstance = null;

    /** @return self */
    public static function getInstance(): self
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    protected function __construct()
    {
        $this->_className = basename(__FILE__, '.php');
    }

    /**
     * 首頁
     *
     * @return void
     */
    public function home(): void
    {
        $pageTitle = '首頁';
        $headerTitle = Constant::CustomerFullName. ' ' . Constant::SystemName;

        $template = 'Main._home';

        $pageContext = 'home';

        $scripts = $this->_buildScriptHTML([
            '/js/main/home.js'
        ]);

        view('Main.Index', compact(
            'pageTitle',
            'headerTitle',
            'template',
            'pageContext',
            'scripts'
        ));
    }

    /**
     * 借還書頁
     *
     * @return void
     */
    public function circulation(): void
    {
        $pageTitle = '借還書作業';
        $headerTitle = Constant::CustomerFullName. ' ' . Constant::SystemName;

        $template = 'Main._circulation';

        $pageContext = 'circulation';

        $scripts = $this->_buildScriptHTML([
            '/js/main/circulation.js'
        ]);

        view('Main.Index', compact(
            'pageTitle',
            'headerTitle',
            'template',
            'pageContext',
            'scripts'
        ));
    }

    /**
     * 圖書管理頁
     *
     * @return void
     */
    public function books(): void
    {
        $pageTitle = '圖書管理作業';
        $headerTitle = Constant::CustomerFullName. ' ' . Constant::SystemName;

        $template = 'Main._books';

        $pageContext = 'books';

        $scripts = $this->_buildScriptHTML([
            '/js/main/books.js'
        ]);

        view('Main.Index', compact(
            'pageTitle',
            'headerTitle',
            'template',
            'pageContext',
            'scripts'
        ));
    }

    /**
     * 借閱者/會員管理頁
     *
     * @return void
     */
    public function members(): void
    {
        $pageTitle = Constant::MemberCall . '管理作業';
        $headerTitle = Constant::CustomerFullName. ' ' . Constant::SystemName;

        $template = 'Main._members';

        $pageContext = 'members';

        $scripts = $this->_buildScriptHTML([
            '/js/main/members.js'
        ]);

        view('Main.Index', compact(
            'pageTitle',
            'headerTitle',
            'template',
            'pageContext',
            'scripts'
        ));
    }
}
