<?php

namespace App\Controllers\Web;

use App\Controllers\WebPageController;
use App\Constant;
use App\Handlers\BookHandler;
use App\Handlers\StorageTypeHandler;

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

        $pageContext = 'Home';

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

        $pageContext = 'Borrow';

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

        $pageContext = 'Return';

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

        $pageContext = 'BookList';

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

        $template = 'Main.book._form';

        $pageContext = 'AddBook';

        $storageTypes = StorageTypeHandler::getInstance()->getAll();

        $scripts = $this->_buildScriptHTML([
            '/js/main/book/form.js'
        ]);

        view('Main.Index', compact(
            'pageTitle',
            'headerTitle',
            'template',
            'pageContext',
            'storageTypes',
            'scripts'
        ));
    }

    /**
     * 編輯圖書頁
     *
     * @param  string  $bookId  書籍 ID（主鍵）
     * @return void
     */
    public function editBook(string $bookId): void
    {
        $pageTitle = '編輯圖書 - 圖書管理作業';
        $headerTitle = Constant::CustomerFullName. ' ' . Constant::SystemName;

        $template = 'Main.book._form';

        $pageContext = 'EditBook';

        $bookData = BookHandler::getInstance()->getBook($bookId);

        $storageTypes = StorageTypeHandler::getInstance()->getAll();

        $scripts = $this->_buildScriptHTML([
            '/js/main/book/form.js'
        ]);

        view('Main.Index', compact(
            'pageTitle',
            'headerTitle',
            'template',
            'pageContext',
            'bookData',
            'storageTypes',
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

        $pageContext = 'MemberList';

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
