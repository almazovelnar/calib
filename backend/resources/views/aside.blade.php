<aside class="aside aside-fixed">
    <div class="aside-header">
        <a href="{{ route('index') }}" class="aside-logo">Caliber<span>Admin</span></a>
        <a href="" class="aside-menu-link">
            <i data-feather="menu"></i>
            <i data-feather="x"></i>
        </a>
    </div>
    <div class="aside-body">
        <div class="aside-loggedin">
            <div class="d-flex align-items-center justify-content-start">
                <a href="" class="avatar avatar-online"><img src="{{ asset('content/profile.png') }}" class="rounded-circle" alt=""></a>
                <div class="aside-alert-link">
                    <a href="{{ route('news.create') }}" class="" ><i data-feather="plus"></i></a>
                    <a href="{{ route('notifications') }}" class="{{ auth()->user()->unreadNotifications->count() == 0 ?:'new' }} mr-4" data-toggle="tooltip" title="У вас {{ auth()->user()->unreadNotifications->count() }} новых уведомлений"><i data-feather="bell"></i></a>
                    <a href="javascript:void(0)" onclick="confirm('Вы точно хотите выйти?') ? document.getElementById('logout-form').submit() :''" data-toggle="tooltip" data-placement="top" title="Logout"><i data-feather="log-out"></i></a>
                </div>
            </div>
            <div class="aside-loggedin-user">
                <a href="#loggedinMenu" class="d-flex align-items-center justify-content-between mg-b-2" data-toggle="collapse">
                    <h6 class="tx-semibold mg-b-0">{{ ucfirst(auth()->user()->name) }}</h6>
                    <i data-feather="chevron-down"></i>
                </a>
                <p class="tx-color-03 tx-12 mg-b-0">{{ ucfirst(auth()->user()->position) }}</p>
            </div>
            <div class="collapse" id="loggedinMenu">
                <ul class="nav nav-aside mg-b-0">
                    <li class="nav-item"><a href="{{ route('user.edit') }}" class="nav-link"><i data-feather="edit"></i> <span>Edit Profile</span></a></li>
                    <li class="nav-item"><a href="javascript:void(0)" onclick="confirm('Вы точно хотите выйти?') ? document.getElementById('logout-form').submit() : ''" class="nav-link"><i data-feather="log-out"></i> <span>Sign Out</span></a></li>
                    <form action="{{ route('logout') }}" id="logout-form" method="POST">@csrf</form>
                </ul>
            </div>
        </div>
        <ul class="nav nav-aside">
            <li class="nav-label">Меню</li>

            <li class="nav-item {{ !Request::is('category','category/*') ?: 'active' }}">
                <a href="{{ route('category.index') }}" class="nav-link"><i data-feather="bookmark"></i> <span>Категории</span></a>
            </li>

            <li class="nav-item {{ Request::is('news','news/*') && Request::query('published') === 'true' ? 'active' :'' }}">
                <a href="{{ route('news.index', ['published' => 'true']) }}" class="nav-link"><i data-feather="layout"></i> <span>Опубликованные</span></a>
            </li>

            <li class="nav-item {{ Request::is('news','news/*') && Request::query('published') === 'false' ? 'active' :'' }}">
                <a href="{{ route('news.index', ['published' => 'false']) }}" class="nav-link"><i data-feather="layout"></i> <span>Неопубликованные</span></a>
            </li>

            <li class="nav-item {{ Request::is('news','news/*') && Request::query('deleted') === 'true' ? 'active' :'' }}">
                <a href="{{ route('news.index', ['deleted' => 'true']) }}" class="nav-link"><i data-feather="layout"></i> <span>Удаленные</span></a>
            </li>

            <li class="nav-item {{ Request::is('stats','stats/*') ? 'active' :'' }}">
                <a href="{{ route('stats') }}" class="nav-link"><i data-feather="layout"></i> <span>Статистика</span></a>
            </li>

            <li class="nav-item {{ Request::is('popular','popular/*') ? 'active' :'' }}">
                <a href="{{ route('popular') }}" class="nav-link"><i data-feather="layout"></i> <span>Популярный</span></a>
            </li>

            <li class="nav-label mg-t-25">Настройки</li>
            <li class="nav-item {{ !Request::is('user','user/*') ?: 'active' }}">
                <a href="{{ route('user.index') }}" class="nav-link"><i data-feather="user"></i> <span>Пользователи</span></a>
            </li>
            <li class="nav-item {{ !Request::is('settings','settings/*') ?: 'active' }}">
                <a href="{{ route('settings') }}" class="nav-link"><i data-feather="settings"></i> <span>Горячая новость</span></a>
            </li>
            <li class="nav-item {{ !Request::is('banner','banner/*') ?: 'active' }}">
                <a href="{{ route('banner') }}" class="nav-link"><i data-feather="settings"></i> <span>Banner</span></a>
            </li>
            <li class="nav-item {{ !Request::is('about-us','about-us/*') ?: 'active' }}">
                <a href="{{ route('about-us') }}" class="nav-link"><i data-feather="settings"></i> <span>О нас</span></a>
            </li>
        </ul>
    </div>
</aside>
