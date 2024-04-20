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
                        <a class="nav-link" href="layouts-default.html">
                            <i class="bx bx-home-alt" aria-hidden="true"></i>
                            <span>Dashboard</span>
                        </a>                        
                    </li>
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="bx bx-cart-alt" aria-hidden="true"></i>
                            <span>Members</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="ecommerce-dashboard.html">
                                    List Members
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="ecommerce-products-list.html">
                                    Register New Member
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="bx bx-file" aria-hidden="true"></i>
                            <span>Pages</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="pages-signup.html">
                                    Sign Up
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="pages-signin.html">
                                    Sign In
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="pages-user-profile.html">
                                    User Profile
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="pages-timeline.html">
                                    Timeline
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="pages-media-gallery.html">
                                    Media Gallery
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="pages-invoice.html">
                                    Invoice
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="pages-404.html">
                                    404
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="pages-500.html">
                                    500
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="pages-log-viewer.html">
                                    Log Viewer
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="pages-search-results.html">
                                    Search Results
                                </a>
                            </li>
                        </ul>
                    </li> -->
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