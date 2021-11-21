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
     * 借還書作業 - 借書頁
     *
     * @return void
     */
    public function borrow(): void
    {
        $pageTitle = '借書 - 借還書作業';
        $headerTitle = Constant::CustomerFullName. ' ' . Constant::SystemName;

        $template = 'Main._borrow';

        $pageContext = 'borrow';

        $scripts = $this->_buildScriptHTML([
            '/js/main/borrow.js'
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
     * 借還書作業 - 還書頁
     *
     * @return void
     */
    public function return(): void
    {
        $pageTitle = '還書 - 借還書作業';
        $headerTitle = Constant::CustomerFullName. ' ' . Constant::SystemName;

        $template = 'Main._return';

        $pageContext = 'return';

        $scripts = $this->_buildScriptHTML([
            '/js/main/return.js'
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
