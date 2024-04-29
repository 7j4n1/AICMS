@props(['homeUrl', 'sectionName', 'subSection1', 'subSection2'])
<?php $dashLogoUrl = "media/logo/4a7as2ssw24j8hG6slbirlogo.png"; ?>
<section class="body">

<!-- start: header -->
<header class="header">
    <div class="logo-container">
        <a href="{{ $homeUrl }}" class="logo">
            <img src="{{ asset($dashLogoUrl) }}" width="55" height="35" alt="Al-Birru" />
        </a>

        <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>

    </div>

    <!-- start: search & user box -->
    <div class="header-right">

        <form action="#" class="search nav-form">
            <div class="input-group">
                <input type="text" class="form-control" name="q" id="q" placeholder="Search...">
                <button class="btn btn-default" type="button"><i class="bx bx-search"></i></button>
            </div>
        </form>


        <div id="userbox" class="userbox">
            <a href="#" data-bs-toggle="dropdown">
                <figure class="profile-picture">
                    <!-- <img src="img/!logged-user.jpg" alt="Joseph Doe" class="rounded-circle" data-lock-picture="img/!logged-user.jpg" /> -->
                </figure>
                <div class="profile-info" data-lock-name="John Doe" data-lock-email="{{auth('admin')->user()->email}}">
                    <span class="name">{{ auth('admin')->user()->name }}</span>
                    {{-- check if the authenticated user has role of super-admin or manager --}}
                    @if(auth('admin')->user()->hasRole('super-admin'))
                        <span class="role">Administrator</span>
                    @elseif(auth('admin')->user()->hasRole('manager'))
                        <span class="role">Manager</span>
                    @endif
                </div>

                <i class="fa custom-caret"></i>
            </a>

            <div class="dropdown-menu">
                <ul class="list-unstyled mb-2">
                    <li class="divider"></li>
                    <li>
                        <a role="menuitem" tabindex="-1" href="{{route('admins')}}"><i class="bx bx-user-circle"></i> My Profile</a>
                    </li>
                    <li>
                        <a role="menuitem" tabindex="-1" href="{{ route('logout') }}"><i class="bx bx-power-off"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- end: search & user box -->
</header>
<!-- end: header -->

<div class="inner-wrapper">
    
    <x-navbars.sidebarnav>

    </x-sidebarnav>

    <section role="main" class="content-body">
        <header class="page-header">
            <h2>{{ $sectionName }}</h2>

            <div class="right-wrapper text-end">
                <ol class="breadcrumbs">
                    <li>
                        <a href="{{ $homeUrl }}">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>

                    <li><span>{{$subSection1}}</span></li>

                    <li><span>{{$subSection2}}</span></li>

                </ol>

                <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fas fa-chevron-left"></i></a>
            </div>
        </header>