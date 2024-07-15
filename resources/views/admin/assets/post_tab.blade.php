<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ request()->is('home') ? 'active' : '' }}" href="{{ url('/home') }}">Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is('users') ? 'active' : '' }}" aria-current="page" href="{{ url('/users') }}">Users</a>
    </li>
 
    <li class="nav-item">
        <a class="nav-link {{ request()->is('admin/posts') ? 'active' : '' }}" href="{{ url('/admin/posts') }}">Posts</a>
    </li>
</ul>