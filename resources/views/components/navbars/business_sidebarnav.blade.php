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
                        <a class="nav-link" href="{{route('dashboard')}}" wire:navigate>
                            <i class="bx bx-home-alt" aria-hidden="true"></i>
                            <span>Main Dashboard</span>
                        </a>                        
                    </li>

                    @canany(['can edit', 'can delete'], 'admin')
                    
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="bx bx-cart-alt" aria-hidden="true"></i>
                            <span>Items & Category</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="{{route('members')}}" wire:navigate>
                                    Categories
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{route('members')}}" wire:navigate>
                                    Items Capture
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                    <li>
                        <a class="nav-link" href="{{ route('individualReport') }}">
                            <i class="bx bx-cart-alt" aria-hidden="true"></i>
                            <span>Repayment Capture</span>
                        </a>
                    </li>
                    
                    {{-- <li class="nav-parent">
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
                    </li> --}}
                    <!-- if the authenticated user can edit and can delete -->
                    
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="bx bx-file" aria-hidden="true"></i>
                            <span>Reports</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="{{ route('individualReport') }}">
                                    Individual History
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{ route('generalReport') }}">
                                    Payment Defaulters
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{ route('activeLoansReport') }}">
                                    Repay Records(Annually)
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{route('defaulterLoansReport')}}">
                                    Active Loans
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                    @endcanany
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