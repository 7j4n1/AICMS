<div class="container">
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Monthly/Yearly Purchase History</h2>
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
                                                <h4 class="title">Total Purchase</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($total, 2)}}</strong>
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
                                                <h4 class="title">Total Paid</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($total_paid, 2)}}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                                <a class="text-muted text-uppercase" href="#">(paid)</a>
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
                                            </div>
                                        </div>
                                        <div class="widget-summary-col">
                                            <div class="summary">
                                                <h4 class="title">Active No</h4>
                                                <div class="info">
                                                    <strong class="amount">{{ $this->getActiveStatusByDate }}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                                <a class="text-muted text-uppercase" href="#">(status)</a>
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
                                                <h4 class="title">Outstanding Purchase</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($total_balance, 2)}}</strong>
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
        
        <div class="col-md-3 mb-2">
            <!-- Date Range input type -->
            <div class="form-group">
                <label for="beginning_date">Date From</label>
                <input type="date" name="beginning_date" id="beginning_date" class="form-control" wire:model="beginDate">
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <!-- Date Range input type -->
            <div class="form-group">
                <label for="daterange">To Date</label>
                <input type="date" name="daterange" id="daterange1" class="form-control" wire:model="endDate">
            </div>
        </div>
        <div class="col-md-3 mb-4" style="padding-top: 2%;">
            <button type="submit" class="btn btn-primary" >Search</button>
        </div>
        {{--@if($ledgers->count() > 0)
            <div class="col-md-3 mb-4" style="padding-top: 2%;">
                <!-- <button type="button" class="btn btn-primary" @click="downloadLedger('{{$coopId}}')">Export to Pdf</a> -->
                <a href="{{ route('individualReportDownload', ['id' => $coopId, 'beginning_date' => $beginning_date, 'ending_date' => $ending_date]) }}" class="btn btn-primary">Export to Pdf</a>
            </div>
        @endif --}}

    </form>
    
    
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">General History</h2>
                </header>
                <div class="card-body">
                
                    {{-- Payment Records Table --}}
                    <!-- class="table-responsive"> -->
                        <table class="table table-bordered table-striped mb-0" id="datatable-tabletools">

                            <thead>
                                <tr>
                                    <th>Coop Id</th>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>From </th>
                                    <th>Duration</th>
                                    <th>To </th>
                                    <th>Status</th>
                                    <th>Paid</th>
                                    <th>Bal</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($this->itemcaptures as $itemcapture)
                                    <tr wire:key="item-individualpurchase-{{ $itemcapture->id }}">
                                        <td>{{ $itemcapture->coopId }}</td>
                                        <td>{{ $itemcapture->category->name }}</td>
                                        <td>{{ number_format($itemcapture->category->price, 2) }}</td>
                                        <td>{{ $itemcapture->buyingDate }}</td>
                                        <td>{{ $itemcapture->payment_timeframe }}</td>
                                        <td>{{ $itemcapture->repaymentDate }}</td>
                                        <td>{{ $itemcapture->payment_status }}</td>
                                        <td>{{ number_format($itemcapture->loanPaid, 2) }}</td>
                                        <td>{{ number_format($itemcapture->loanBalance, 2) }}</td>
                                        
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