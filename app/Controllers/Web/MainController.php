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

        $template = 'Main.circulation._borrow';

        $pageContext = 'borrow';

        $scripts = $this->_buildScriptHTML([
            '/js/main/circulation/borrow.js'
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

        $template = 'Main.circulation._return';

        $pageContext = 'return';

        $scripts = $this->_buildScriptHTML([
            '/js/main/circulation/return.js'
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
     * 圖書管理（列表）頁
     *
     * @return void
     */
    public function books(): void
    {
        $pageTitle = '圖書管理作業';
        $headerTitle = Constant::CustomerFullName. ' ' . Constant::SystemName;

        $template = 'Main.book._list';

        $pageContext = 'books';

        $scripts = $this->_buildScriptHTML([
            '/js/main/book/list.js'
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
     * 新增圖書頁
     *
     * @return void
     */
    public function addBook(): void
    {
        $pageTitle = '新增圖書 - 圖書管理作業';
        $headerTitle = Constant::CustomerFullName. ' ' . Constant::SystemName;

        $template = 'Main.book._add';

        $pageContext = 'addBook';

        $scripts = $this->_buildScriptHTML([
            '/js/main/book/add.js'
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

        $template = 'Main.member._list';

        $pageContext = 'members';

        $scripts = $this->_buildScriptHTML([
            '/js/main/member/list.js'
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
