<aside class="navbar fixed-left float-start align-items-start py-0">
    <nav id="leftNav" class="nav nav-tabs flex-column border-0">
        <a id="homeTab" class="nav-link fs-r1.15 border-0 pt-3" href="/home">首頁</a>
        <!-- <div id="circulationTab" class="nav-link fs-r1.15 border-0 py-3" role="button" data-bs-toggle="collapse" data-bs-target="#cicrulationSubTab">借還書作業</div>
        <div id="cicrulationSubTab" class="fs-r1 mb-2 collapse show">
            <a id="borrowTab" class="nav-link border-0 ps-4.5" href="/circulation/borrow">借書</a>
            <a id="returnTab" class="nav-link border-0 ps-4.5" href="/circulation/return">還書</a>
        </div> -->
        <div id="circulationTab" class="nav-link nav-link-default fs-r1.15 border-0 py-3">借還書作業</div>
        <div id="cicrulationSubTab" class="fs-r1 mb-2">
            <a id="borrowTab" class="nav-link border-0 ps-4.5" href="/circulation/borrow">借書</a>
            <a id="returnTab" class="nav-link border-0 ps-4.5" href="/circulation/return">還書</a>
        </div>
        <a id="bookTab" class="nav-link fs-r1.15 border-0 py-3" href="/books?t=No">圖書管理作業</a>
        <div id="bookSubTab" class="fs-r1 mb-2">
            <a id="addBookTab" class="nav-link border-0 ps-4.5" href="/book/add">新增圖書</a>
        </div>
        <a id="memberTab" class="nav-link fs-r1.15 border-0 py-3" href="/members"><?= App\Constant::MemberCall ?>管理作業</a>
    </nav>
</aside>
