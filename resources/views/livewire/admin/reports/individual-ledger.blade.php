<div class="container">
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    @if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('member', 'admin'))
                        <h2 class="card-title">My Personal Ledger</h2>
                    @else
                        <h2 class="card-title">Individual Ledger</h2>
                    @endif
                </header>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
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
                        <div class="col-md-6">
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
                        <div class="col-md-6">
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
                                                <h4 class="title">Outstanding Loans</h4>
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
                </div>
            </section>
        </div>
    </div>

    
    <!-- Button to open the modal for capturing new payment details -->
    <form wire:submit="searchResult" class="row g-3">
        <input type="hidden" name="_token" value="{{ $csrf_token }}">
        @canany(['can edit', 'can delete'], 'admin')
        <div class="col-md-2 mb-2">
            <div class="form-group">
                <label for="coopId">CoopId</label>
                <select class="form-select" wire:model.live="coopId">
                    <option value=""></option>
                    @foreach($memberIds as $memberId)
                        <option value="{{ $memberId->coopId }}">{{ $memberId->coopId }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endcanany
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
            <button type="submit" class="btn btn-primary" >Search</button>
        </div>
        @if($ledgers->count() > 0)
            <div class="col-md-3 mb-4" style="padding-top: 2%;">
                <!-- <button type="button" class="btn btn-primary" @click="downloadLedger('{{$coopId}}')">Export to Pdf</a> -->
                @canany(['can edit', 'can delete'], 'admin')
                    
                @endcanany
                @if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('member', 'admin'))
                    <a href="{{ route('individualReportDownload', ['id' => $coopId, 'beginning_date' => $beginning_date, 'ending_date' => $ending_date]) }}" class="btn btn-primary">Export to Pdf</a>
                @else
                <a href="{{ route('individualReportDownloadAdmin', ['id' => $coopId, 'beginning_date' => $beginning_date, 'ending_date' => $ending_date]) }}" class="btn btn-primary">Export to Pdf</a>
                @endif

            </div>
        @endif

    </form>
    
    
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    @if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole(['member'], 'admin'))
                        <h2 class="card-title">My Personal Ledger</h2>
                    @else
                        <h2 class="card-title">All Individual Ledgers</h2>
                    @endif
                </header>
                <div class="card-body">
                
                    {{-- Payment Records Table --}}
                    <!-- class="table-responsive"> -->
                        <table class="table table-bordered table-striped mb-0" id="datatable-tabletools">

                            <thead>
                                <tr>
                                    <th>Coop Id</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Savings(&#8358;)</th>
                                    <th>Shares(&#8358;)</th>
                                    <th>Loans(&#8358;)</th>
                                    <th>Others(&#8358;)</th>
                                    <th>Admin Charge(&#8358;)</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($ledgers as $ledger)
                                    <tr wire:key="item-individualledger-{{ $ledger->id }}">
                                        <td>{{ $ledger->coopId }}</td>
                                        <td>{{ number_format($ledger->totalAmount, 2) }}</td>
                                        <td>{{ $ledger->paymentDate }}</td>
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

    <script>
        function sendMsg(value) {
            Livewire.dispatch('download-payments', { id: value });
        }
        function downloadLedger(value) {
            console.log(value);
            Livewire.dispatch('on-downloadLedger', { id: value });
        }

    </script>
    

</div>