<!DOCTYPE html>
<html lang="{{  str_replace('_', '-', app()->getLocale())  }}" class="" >
	<head>

		<!-- Basic -->
        <!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> -->
		<!-- <meta charset="Unicode"> -->
        <meta charset="utf-8">

		<title>{{--$memberId->surname--}} Ledger  "Admin Dashboard" | Al-Birru</title>

		<meta name="keywords" content="cooperative, islamic, society, Al-birru" />
		<meta name="description" content="Al-Birr Islamic Cooperative Multipurpose society">
		<meta name="author" content="albirr">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
        @include('components.layouts.partials.styles')
        @include('components.layouts.partials.jquery')
        @include('components.layouts.partials.bootstrapjs')
        @include('components.layouts.partials.fontawesome')
        <style>
            .col-md-3 {
                margin-bottom: 4px;
                /* margin-left: -18px; */
                /* padding: 5px; */
            }
            .rounded .panel-title {
                /* font-size: 1em; */
                /* padding: 2px; */
                font-weight:700;
                font-size: smaller;
            }

            .rounded .text-capitalize {
                font-size: small;
            }
            .naira::before {
                content: "\20A6";
            }
        </style>
        

	</head>
	<body class="container-fluid">
        <!-- Start: Main -->
        <!-- <div class=""> -->
            <div class="row row-no-gutters">
                <div class="col-md-12 col-sm-12 mb-4 ">
                    <div class="panel panel-default" style="margin-top: 2px;">
                        <div class="panel-heading">
                            <h3 class="panel-title">Account Information</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row row-no-gutters">
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Account Name</div>
                                        <div class="panel-title p-2"><span class="text-bold"> {{$memberId->surname}} {{$memberId->otherNames}}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Total Amounts</div>
                                        <div class="panel-title p-2"><span class="text-bold">NGN {{number_format($dataTotals->totalAmount, 2)}} </span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Total Savings</div>
                                        <div class="panel-title p-2"><span class="text-bold">NGN {{number_format($dataTotals->savingAmount, 2)}}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Loan Status</div>
                                        <div class="panel-title p-2"><span class="text-bold"> {{$isOnLoan ? 'Active' : 'Not Active'}}</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-no-gutters">
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Account Coop ID</div>
                                        <div class="panel-title p-2"><span class="text-bold"> {{$memberId->coopId}}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Total Shares</div>
                                        <div class="panel-title p-2"><span class="text-bold">NGN {{number_format($dataTotals->shareAmount, 2)}} </span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Total Loans</div>
                                        <div class="panel-title p-2"><span class="text-bold">NGN {{number_format($dataTotals->loanAmount, 2)}}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Admin & Others</div>
                                        <div class="panel-title p-2"><span class="text-bold">NGN {{number_format($dataTotals->adminCharge + $dataTotals->others, 2)}}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-no-gutters">
                <div class="col-md-12 col-sm-12 mb-4 col-no-gutters">
                    <div class="panel" style="margin-top: 5px;">
                        
                        <div class="panel-body">
                            <div class="row row-no-gutters">
                                <div class="col-md-4 col-sm-4 col-xs-4 col-no-gutters">
                                    <div class="rounded">
                                        <div class="p-2 ">Date printed</div>
                                        <div class="panel-title p-2"><span class="text-bold"> {{date('d-M-Y')}}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4 mb-2 col-xs-4 col-no-gutters">
                                    <div class="rounded">
                                        <div class="p-2 ">From date</div>
                                        <div class="panel-title p-2"><span class="text-bold">{{ $beginning_date }}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4 mb-2 col-xs-4 col-no-gutters">
                                    <div class="rounded">
                                        <div class="p-2 ">To date</div>
                                        <div class="panel-title p-2"><span class="text-bold">{{ $ending_date }}</span></div>
                                    </div>
                                </div>
                                
                            </div>

                            
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-striped table-bordered table-responsive">
                <thead>
                    <tr class="active">
                        <th>Date</th>
                        <th>Amount(#)</th>
                        <th>Savings(#)</th>
                        <th>Shares(#)</th>
                        <th>Loans(#)</th>
                        <th>Others(#)</th>
                        <th>Admin(#)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ledgers as $ledger)
                        <tr wire:key="item-individualledger-{{ $ledger->id }}">
                            <td>{{ date("d-m-Y", strtotime($ledger->paymentDate)) }}</td>
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
    </body>
</html>