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
                <label for="beginning_date">Loan Type</label>
                <select name="datatable-tabletools_length" class="form-select form-select-sm w-auto" data-select2-id="1" wire:model.live="loanType">
                    <option value="normal" data-select2-id="3">Normal</option>
                    <option value="special" data-select2-id="18">Special</option>
                </select>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <!-- Date Range input type -->
            <div class="form-group">
                <label for="daterange">To Date</label>
                <input type="date" name="daterange" id="daterange1" class="form-control" wire:model.live="ending_date">
            </div>
        </div>

    </form>
    
    
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Loan Repayment(Active) Report</h2>
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
                                                <h4 class="title">Total Loan</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($total_loans, 2)}}</strong>
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
                                                <h4 class="title">Total Expected Balance</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($total_balance, 2)}}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
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
                                    <th>Loan Date</th>
                                    <th>Repayment Date</th>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Loan Amount(&#8358;)</th>
                                    <th>Total Paid(&#8358;)</th>
                                    <th>Balance(&#8358;)</th>
                                    <th>Last Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($activeLoans as $activeLoan)
                                    <tr wire:key="item-generalactiveLoan-{{ $activeLoan->id }}">
                                        <td>{{ date('M-Y',strtotime($activeLoan->loanDate)) }}</td>    
                                        <td>{{ date('M-Y',strtotime($activeLoan->repaymentDate)) }}</td>
                                        <td>{{ $activeLoan->coopId }}</td>
                                        <td>{{ $activeLoan->member->surname }} {{$activeLoan->member->otherNames}}</td>
                                        <td>{{ number_format($activeLoan->loanAmount, 2) }}</td>
                                        <td>{{ number_format($activeLoan->loanPaid, 2) }}</td>
                                        <td>{{ number_format($activeLoan->loanBalance, 2) }}</td>
                                        <td>{{ date('M-Y',strtotime($activeLoan->repaymentDate)) }}</td>
                                    </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                        
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