<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ request()->is('home') ? 'active' : '' }}" href="{{ url('/home') }}">Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is('posts') ? 'active' : '' }}" aria-current="page" href="{{ url('/posts') }}">Post</a>
    </li>
 
    <li class="nav-item">
        <a class="nav-link {{ request()->is('link') ? 'active' : '' }}" href="{{ url('/link') }}">Link</a>
    </li>
</ul>