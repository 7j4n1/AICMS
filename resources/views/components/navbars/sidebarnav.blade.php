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
                    {{-- Members --}}
                    @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('member', 'admin'))
                        <li>
                            <a class="nav-link" href="{{route('user.dashboard')}}" wire:navigate>
                                <i class="bx bx-home-alt" aria-hidden="true"></i>
                                <span>My Dashboard</span>
                            </a>                        
                        </li>
                        <li>
                            <a class="nav-link" href="{{ route('user.individualReport') }}">
                                <i class="bx bx-file" aria-hidden="true"></i>
                                <span>Personal Ledger</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="{{ route('user.purchase.individualReport') }}">
                                <i class="bx bx-data" aria-hidden="true"></i>
                                <span>Purchase History</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="{{ route('user.logout') }}">
                                <i class="bx bx-power-off"></i> Logout
                            </a>
                        </li>
                    @endif
                    {{-- End Members --}}


                    {{-- Admins --}}
                    
                    @canany(['can create','can edit'], 'admin')
                    <li>
                        <a class="nav-link" href="{{route('dashboard')}}" wire:navigate>
                            <i class="bx bx-home-alt" aria-hidden="true"></i>
                            <span>My Dashboard</span>
                        </a>                        
                    </li>
                    <li>
                        <a class="nav-link" href="{{route('business.categories')}}" >
                            <i class="bx bx-home-alt" aria-hidden="true"></i>
                            <span>Business Dashboard</span>
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
                            <i class="bx bx-file" aria-hidden="true"></i>
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
                            <i class="bx bx-file" aria-hidden="true"></i>
                            <span>Accounts Management</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="{{route('payments')}}">
                                    Payment capture
                                </a>
                            </li>

                            <li>
                                <a class="nav-link" href="{{route('sp-save-deduction')}}">
                                    Special Save Deduct
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="bx bx-file" aria-hidden="true"></i>
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
                            <i class="bx bx-file" aria-hidden="true"></i>
                            <span>Imports/Export</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="{{route('importallMembers')}}">
                                    Imports records
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{route('importMembersCsv')}}">
                                    Import Members CSV
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{route('importLedgerCsv')}}">
                                    Import Ledger CSV
                                </a>
                            <li>
                                <a class="nav-link" href="{{route('importLoansCsv')}}">
                                    Import Loans CSV
                                </a>
                            <li>
                                <a class="nav-link" href="{{route('exports')}}">
                                    Export records
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                    <!-- if the authenticated user can edit and can delete -->
                    <li>
                        <a class="nav-link" href="{{ route('annualFees') }}">
                            <i class="bx bx-file" aria-hidden="true"></i>
                            <span>Annual Fees</span>
                        </a>
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
                    {{--<li>
                        <a class="nav-link" href="{{route('backup.index')}}">
                        <i class='bx bx-data' aria-hidden="true"></i>
                            <span>Backup</span>
                        </a>                        
                    </li>--}}
                    <li>
                        <a class="nav-link" href="{{ route('logout') }}">
                            <i class="bx bx-power-off"></i> Logout
                        </a>
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