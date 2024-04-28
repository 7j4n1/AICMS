<div class="container">
    <!-- Button to open the modal for capturing new payment details -->
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Loan Repayment(Defaulters) Report</h2>
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
                                                <h4 class="title">Total Loans</h4>
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
                                    <th>Default Days</th>
                                    <th>Id</th>
                                    <th>Loan Amount(&#8358;)</th>
                                    <th>Balance(&#8358;)</th>
                                    <th>Guarantor 1</th>
                                    <th>Guarantor 2</th>
                                    <th>Guarantor 3</th>
                                    <th>Guarantor 4</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($loans as $loan)
                                    <tr wire:key="item-generalloan-{{ $loan->id }}">
                                        <td>{{ date('d-M-Y',strtotime($loan->loanDate)) }}</td> 
                                           <!-- Days interval between repayment Date and Today  -->
                                        @php
                                            $date1 = new DateTime($loan->repaymentDate);
                                            $date2 = new DateTime(date('Y-m-d'));
                                            $interval = $date1->diff($date2);
                                            $date_ = $interval->format('%R%a days');
                                        @endphp
                                        <td>{{ $date_ }}</td>
                                        <td>{{ $loan->coopId }}</td>
                                        <td>{{ number_format($loan->loanAmount, 2) }}</td>
                                        @php
                                            $balance = \App\Models\ActiveLoans::where('coopId', $loan->coopId)->first()->loanBalance;
                                            
                                        @endphp
                                        <td>{{ number_format($balance, 2) }}</td>
                                        @if($loan->guarantor1()->first() == null)
                                            <td></td>
                                        @else
                                            <td>{{ $loan->guarantor1()->first()->surname }}/{{$loan->guarantor1()->first()->phoneNumber}}</td>
                                        @endif
                                        @if($loan->guarantor2()->first() == null)
                                            <td></td>
                                        @else
                                            <td>{{ $loan->guarantor2()->first()->surname }}/{{$loan->guarantor2()->first()->phoneNumber}}</td>
                                        @endif
                                        @if($loan->guarantor3()->first() == null)
                                            <td></td>
                                        @else
                                            <td>{{ $loan->guarantor3()->first()->surname }}/{{$loan->guarantor3()->first()->phoneNumber}}</td>
                                        @endif
                                        @if($loan->guarantor4()->first() == null)
                                            <td></td>
                                        @else
                                            <td>{{ $loan->guarantor4()->first()->surname }}/{{$loan->guarantor4()->first()->phoneNumber}}</td>
                                        @endif
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