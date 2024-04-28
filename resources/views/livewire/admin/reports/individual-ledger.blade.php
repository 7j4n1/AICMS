<div class="container">
    <!-- Button to open the modal for capturing new payment details -->
    <form wire:submit="searchResult" class="row g-3">
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
                <a href="{{ route('individualReportDownload', ['id' => $coopId, 'beginning_date' => $beginning_date, 'ending_date' => $ending_date]) }}" class="btn btn-primary">Export to Pdf</a>
            </div>
        @endif

    </form>
    
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">All Individual Ledgers</h2>
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