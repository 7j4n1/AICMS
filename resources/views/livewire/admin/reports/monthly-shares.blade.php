<div class="container">
    <!-- Button to open the modal for capturing new payment details -->
    <form wire:submit="searchResult" class="row g-3">
        <div class="col-md-3 mb-2">
            <!-- Date Range input type -->
            <div class="form-group">
                <label for="year">Select Year</label>
                <select class="form-select" wire:model.live="year">
                    <!-- default option is current year -->
                    @foreach(range(2023, 2040) as $year1)
                        <option value="{{ $year1 }}">{{ $year1 }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        

    </form>
    
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Monthly Shares Ledger</h2>
                </header>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-xl-12">
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
                                                    <strong class="amount">&#8358; {{number_format($total_shares, 2)}}</strong>
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
                            <!-- <th scope="col">Year</th> -->
                            <th scope="col">Coop ID</th>
                            <th scope="col">Name</th>
                            @for ($i = 1; $i <= 12; $i++)
                                <th scope="col">{{ date('M', mktime(0,0, 0, $i, 1)) }}</th>
                            @endfor
                            
                        </thead>
                        <tbody>
                            
                            @foreach ($shares as $coopId => $share)
                                <tr wire:key="item-monthlysharesledger-{{ $coopId }}">
                                    <td scope="row">{{ $coopId }}</td>
                                    @php
                                        $user = \App\Models\Member::where('coopId', $coopId)->first();
                                        $surname = $user->surname ?? '';
                                        $otherNames = $user->otherNames ?? '';
                                    @endphp
                                    <td scope="row">{{ $surname }} {{ $otherNames }}</td>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <td scope="row">
                                            {{ number_format($share->firstWhere('month', $i)->shareAmount ?? 0, 2) }}
                                        </td>
                                    @endfor
                                </tr>
                            
                            @endforeach
                                {{--<tr wire:key="item-monthlysharesledger-{{ $share[''] }}">
                                    <!-- <td scope="row">{{$year}}</td> -->
                                    <td scope="row">{{$month_day[$share->month]}}</td>    
                                    <td scope="row">{{ number_format($share->shareAmount, 2) }}</td>
                                </tr> --}}
                            
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