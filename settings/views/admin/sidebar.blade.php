<div class="mb-1">
    <a class="menu-point collapsed @if($menuHelper::isActiveMenu($menuHelper::TIME_BOOKING, $url)) active @endif"
       data-toggle="collapse" href="#timeBookingCollapse" role="button"
    >
        <span><i class="fas fa-newspaper mr-1"></i>Időpontfoglaló</span>
        <i class="fa-solid fa-chevron-down"></i>
    </a>
    <div class="collapse collapse-box @if($menuHelper::isActiveMenu($menuHelper::TIME_BOOKING, $url)) show @endif" id="timeBookingCollapse">
        <div class="collapse-menu-points">
            <a href="/booking-list">
                Időpontok <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
    </div>
</div>