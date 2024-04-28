<div class="container">
    <!-- Button to open the modal for capturing new payment details -->
    <form wire:submit="searchResult" class="row g-3">
        <div class="col-md-3 mb-2">
            <!-- Date Range input type -->
            <div class="form-group">
                <label for="beginning_date">Date From</label>
                <input type="date" name="beginning_date" id="beginning_date" class="form-control" wire:model.live="beginning_date">
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <!-- Date Range input type -->
            <div class="form-group">
                <label for="daterange">To Date</label>
                <input type="date" name="daterange" id="daterange1" class="form-control" wire:model.live="ending_date">
            </div>
        </div>
        <div class="col-md-3 mb-4" style="padding-top: 2%;">
            {{--<button type="submit" class="btn btn-primary" >Search</button>--}}
        </div>
        @if($ledgers->count() > 0)
            <div class="col-md-3 mb-4" style="padding-top: 2%;">
                <a href="{{ route('generalReportDownload', ['beginning_date' => $beginning_date, 'ending_date' => $ending_date]) }}" class="btn btn-primary">Export to PDF</a>
            </div>
        @endif

    </form>
    
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">General Members Ledger</h2>
                </header>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-xl-6">
                        <section class="card card-featured-left card-featured-secondary">
                                <div class="card-body">
                                    <div class="widget-summary">
                                        <div class="widget-summary-col widget-summary-col-icon">
                                            <div class="summary-icon bg-secondary">
                                                <i class="fas fa-naira-sign"></i>
                                            </div>
                                        </div>
                                        <div class="widget-summary-col">
                                            <div class="summary">
                                                <h4 class="title">Total Amount</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($total_total, 2)}}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="col-xl-6">
                            <section class="card card-featured-left card-featured-secondary">
                                <div class="card-body">
                                    <div class="widget-summary">
                                        <div class="widget-summary-col widget-summary-col-icon">
                                            <div class="summary-icon bg-secondary">
                                                <i class="fas fa-naira-sign"></i>
                                            </div>
                                        </div>
                                        <div class="widget-summary-col">
                                            <div class="summary">
                                                <h4 class="title">Total Savings</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($total_saving, 2)}}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                                <a class="text-muted text-uppercase" href="#">(savings)</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-xl-6">
                        <section class="card card-featured-left card-featured-secondary">
                                <div class="card-body">
                                    <div class="widget-summary">
                                        <div class="widget-summary-col widget-summary-col-icon">
                                            <div class="summary-icon bg-secondary">
                                                <i class="fas fa-naira-sign"></i>
                                            </div>
                                        </div>
                                        <div class="widget-summary-col">
                                            <div class="summary">
                                                <h4 class="title">Total Shares</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($total_share, 2)}}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                                <a class="text-muted text-uppercase" href="#">(shares)</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="col-xl-6">
                            <section class="card card-featured-left card-featured-secondary">
                                <div class="card-body">
                                    <div class="widget-summary">
                                        <div class="widget-summary-col widget-summary-col-icon">
                                            <div class="summary-icon bg-secondary">
                                                <i class="fas fa-naira-sign"></i>
                                            </div>
                                        </div>
                                        <div class="widget-summary-col">
                                            <div class="summary">
                                                <h4 class="title">Total Loans</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($total_loan, 2)}}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                                <a class="text-muted text-uppercase" href="#">(loans)</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    
                    {{-- Payment Records Table --}}
                    <!-- class="table-responsive"> -->
                        <table class="table table-bordered table-striped mb-0" id="datatable-tabletools">

                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Id</th>
                                    <th>Total Amount(&#8358;)</th>
                                    <th>Total Savings(&#8358;)</th>
                                    <th>Total Shares(&#8358;)</th>
                                    <th>TotalLoans(&#8358;)</th>
                                    <th>Total Others(&#8358;)</th>
                                    <th>Total Admin(&#8358;)</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($ledgers as $ledger)
                                    <tr wire:key="item-generalledger-{{ $ledger->id }}">
                                        <td>{{ date('d-M-Y',strtotime($beginning_date)) }} | {{date('d-M-Y', strtotime($ending_date))}}</td>    
                                        <td>{{ $ledger->coopId }}</td>
                                        <td>{{ number_format($ledger->totalAmount, 2) }}</td>
                                        <td>{{ number_format($ledger->savingAmount, 2) }}</td>
                                        <td>{{ number_format($ledger->shareAmount, 2) }}</td>
                                        <td>{{ number_format($ledger->loanAmount, 2) }}</td>
                                        <td>{{ number_format($ledger->others, 2) }}</td>
                                        <td>{{ number_format($ledger->adminCharge, 2) }}</td>
                                    </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                        
                    <!-- </div> -->
                    <div class="row mt-4">
                        <div class="col-sm-6 offset-5">
                            {{-- $ledgers->links() --}}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    

    <x-scriptvendor></x-scriptvendor>

    
    <!-- Specific Page Vendor -->
    <script src="{{ asset('vendor/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/media/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/JSZip-2.5.0/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/pdfmake-0.1.32/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/pdfmake-0.1.32/vfs_fonts.js') }}"></script>

    

</div>