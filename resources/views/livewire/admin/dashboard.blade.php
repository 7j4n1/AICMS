<div class="container">
    
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
                                <h3 class="">Total Amount</h3>
                                <div class="info">
                                    <strong class="amount">&#8358; {{number_format($total_amounts, 2)}}</strong>
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
                            @if(!auth('admin')->user()->hasRole(['member']))
                                <h3 class="">Total Loans</h3>
                            @else
                                <h3 class="">Outstanding Loan</h3>
                            @endif
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
                                <h3 class="">Total Shares</h3>
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
                                <h3 class="">Total Savings</h3>
                                <div class="info">
                                    <strong class="amount">&#8358; {{number_format($total_savings, 2)}}</strong>
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
    @canany(['can edit', 'can create'])
    <div class="row mb-3">
        <div class="col-xl-6">
            <h3>List of Members</h3>
            <table class="table table-responsive table-striped">
                <thead>
                    <th>Coop Id</th>
                    <th>Surname</th>
                    <th>Phone Number</th>
                </thead>
                <tbody>
                    @foreach($members as $member)
                        <tr>
                            <td>{{$member->coopId}}</td>
                            <td>{{$member->surname}}</td>
                            <td>{{$member->phoneNumber}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @endcanany

        <div class="col-xl-6">
        <h3>List of Active Loans</h3>
            <table class="table table-responsive table-striped">
                <thead>
                    <th>Coop Id</th>
                    <th>Loan Balance (&#8358;)</th>
                    <th>Last Payment Date</th>
                </thead>
                <tbody>
                    @foreach($loans as $loan)
                        <tr>
                            <td>{{$loan->coopId}}</td>
                            <td>{{number_format($loan->loanBalance, 2)}}</td>
                            <td>{{date('d-M-Y', strtotime($loan->lastPaymentDate))}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
        
    </div>

    
</div>
      

<x-scriptvendor></x-scriptvendor>
