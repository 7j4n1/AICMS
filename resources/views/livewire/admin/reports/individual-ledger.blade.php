<div class="container">
    <!-- Button to open the modal for capturing new payment details -->
    <form wire:submit="searchResult" class="row g-3">
        <div class="col-md-2 mb-2">
            <div class="form-group">
                <label for="coopId">CoopId</label>
                <select class="form-select" wire:model="coopId">
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
                <input type="date" name="beginning_date" id="beginning_date" class="form-control" wire:model="beginning_date">
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <!-- Date Range input type -->
            <div class="form-group">
                <label for="daterange">To Date</label>
                <input type="date" name="daterange" id="daterange1" class="form-control" wire:model="ending_date">
            </div>
        </div>
        <div class="col-md-3 mb-4" style="padding-top: 2%;">
            <button type="submit" class="btn btn-primary" >Search</button>
        </div>
        <div class="col-md-3 mb-4" style="padding-top: 2%;">
            <button type="button" class="btn btn-primary" @click="downloadLedger('{{$coopId}}')">Export to Pdf</a>
        </div>

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
                                    <th>Savings</th>
                                    <th>Shares</th>
                                    <th>Loans</th>
                                    <th>Others</th>
                                    <th>Admin Charge</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($ledgers as $ledger)
                                    <tr wire:key="item-profile-{{ $ledger->id }}">
                                        <td>{{ $ledger->coopId }}</td>
                                        <td>{{ $ledger->totalAmount }}</td>
                                        <td>{{ $ledger->paymentDate }}</td>
                                        <td>{{ $ledger->savingAmount }}</td>
                                        <td>{{ $ledger->shareAmount }}</td>
                                        <td>{{ $ledger->loanAmount }}</td>
                                        <td>{{ $ledger->others }}</td>
                                        <td>{{ $ledger->adminCharge }}</td>
                                        
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