<!-- start: sidebar -->
<aside id="sidebar-left" class="sidebar-left">

    <div class="sidebar-header">
        <div class="sidebar-title">
            Navigation
        </div>
        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>

    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">

                <ul class="nav nav-main">
                    <li>
                        <a class="nav-link" href="{{route('dashboard')}}">
                            <i class="bx bx-home-alt" aria-hidden="true"></i>
                            <span>Dashboard</span>
                        </a>                        
                    </li>
                    <li>
                        <a class="nav-link" href="{{route('admins')}}">
                            <i class="bx bx-home-alt" aria-hidden="true"></i>
                            <span>Administrators</span>
                        </a>                        
                    </li>
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="bx bx-cart-alt" aria-hidden="true"></i>
                            <span>Members</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="{{route('members')}}">
                                    List Members
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="bx bx-cart-alt" aria-hidden="true"></i>
                            <span>Accounts Management</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="{{route('payments')}}">
                                    Payment capture
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="bx bx-cart-alt" aria-hidden="true"></i>
                            <span>Loans Management</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="{{route('checkGuarantor')}}">
                                    Check guarantors
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{route('loans')}}">
                                    Loan records
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="bx bx-cart-alt" aria-hidden="true"></i>
                            <span>Imports/Export</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="{{route('importallMembers')}}">
                                    Imports records
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{route('exports')}}">
                                    Export records
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="bx bx-file" aria-hidden="true"></i>
                            <span>Reports</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="{{ route('individualReport') }}">
                                    Individual Reports
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{ route('generalReport') }}">
                                    General Reports
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{ route('activeLoansReport') }}">
                                    Loan Reports
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{route('defaulterLoansReport')}}">
                                    Defaulters Reports
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{ Route('sharesReport') }}">
                                    Shares Reports(Yearly)
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>

        <script>
            // Maintain Scroll Position
            if (typeof localStorage !== 'undefined') {
                if (localStorage.getItem('sidebar-left-position') !== null) {
                    var initialPosition = localStorage.getItem('sidebar-left-position'),
                        sidebarLeft = document.querySelector('#sidebar-left .nano-content');

                    sidebarLeft.scrollTop = initialPosition;
                }
            }
        </script>

    </div>

</aside>
<!-- end: sidebar -->