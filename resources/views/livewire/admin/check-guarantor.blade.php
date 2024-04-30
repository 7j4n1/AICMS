<div class="container">
    <!-- Button to open the modal for capturing new payment details -->
    <form wire:submit="searchResult" class="row g-3">
        <div class="col-md-3 mb-2">
            <!-- Date Range input type -->
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

    </form>
    
    
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Check Guarantor Status</h2>
                </header>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-xl-4">
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
                                                <h4 class="title">Total</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{ number_format(($totalSavings), 2)}}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="col-xl-4">
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
                                                <h4 class="title">Savings</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($allsavings, 2)}}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        
                        <div class="col-xl-4">
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
                                                <h4 class="title">Shares</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($allshares, 2)}}</strong>
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
                    
                    <div class="row mb-3">
                        <div class="col-xl-4">
                            <section class="card card-featured-left card-featured-secondary">
                                <div class="card-body">
                                    <div class="widget-summary">
                                        <div class="widget-summary-col">
                                            <div class="summary">
                                                <h4 class="title">Guarantees</h4>
                                                <div class="info">
                                                    <strong class="amount">{{$guarantees->count()}}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="col-xl-4">
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
                                                <h4 class="title">Total Loan Guaranteed</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($totalLoan_guaranteed, 2)}}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        
                        <div class="col-xl-4">
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
                                                <h4 class="title">Total Outstandings</h4>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{number_format($totalOutstanding, 2)}}</strong>
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
                    
                    <div class="row mb-3">
                        <div class="col-xl-4">
                            <section class="card card-featured-left card-featured-secondary">
                                <div class="card-body">
                                    <div class="widget-summary">
                                        
                                        <div class="widget-summary-col">
                                            <div class="summary">
                                                <h4 class="title">Loan Status</h4>
                                                <div class="info">
                                                    <strong class="amount">{{($guarantor_records->count()) > 0 ? 'Yes' : 'No'}}</strong>
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
                                    <th>Coop</th>
                                    <th>Status</th>
                                    <th>Surname</th>
                                    <th>Phone</th>
                                    <th>Loan Collected(&#8358;)</th>
                                    <th>Balance(&#8358;)</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($guarantees as $guarantee)
                                    <tr wire:key="item-generalguarantee-{{ $guarantee->id }}">
                                        <td>{{ $guarantee->coopId }}</td>    
                                        <td>{{ $guarantee->status }}</td>
                                        <td>{{ $guarantee->user()->surname }}</td>
                                        <td>{{ $guarantee->user()->phoneNumber }}</td>
                                        <td>{{ number_format($guarantee->loanAmount, 2) }}</td>
                                        <td>{{ number_format($guarantee->loanBalance, 2) }}</td>
                                    </tr>
                                @endforeach
                                
                            </tbody>
                        </table>


                    <table class="table table-bordered table-striped mb-0" id="datatable-tabletools2">
                        <thead>
                            <tr>
                                <th>Loan Amount(&#8358;)</th>
                                <th>Coop Number</th>
                                <th>Date Collected</th>
                                <th>Loan Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($guarantor_records as $guarantor_record)
                                <tr wire:key="item-generalguarantor_record-{{ $guarantor_record->id }}">
                                    <td>{{ $guarantor_record->loanAmount }}</td>    
                                    <td>{{ $guarantor_record->coopId }}</td>
                                    <td>{{ date('M-y', strtotime($guarantor_record->loanDate)) }}</td>
                                    <td>{{ ($guarantor_records->count()) > 0 ? '1' : '0' }}</td>
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