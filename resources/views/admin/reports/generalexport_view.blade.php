<?php set_time_limit(240); ?>

<!DOCTYPE html>
<html lang="{{  str_replace('_', '-', app()->getLocale())  }}" class="" >
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">


		<title>General Report Ledger | Al-Birru</title>

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
            .col-md-6 {
                margin-bottom: 4px;
            }
            .rounded .panel-title {
                /* font-size: 1em; */
                /* padding: 2px; */
                font-weight:700;
                font-size: smaller;
            }

            .rounded .text-capitalize {
                font-size: small;
                padding: 2px 0px;
            }
            @font-face {
                font-family: 'dejavu';
                src: url("{{url('fonts/DejaVuSans.ttf')}}") format('truetype');
            }
            .naira {
                font-family: 'dejavu', 'Segoe UI' !important;
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
                            <h3 class="panel-title">General Account Information</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row row-no-gutters">
                                
                                <div class="col-md-6 col-sm-6 mb-2 col-xs-6">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Total Amounts</div>
                                        <div class="panel-title p-2"><span class="text-bold">NGN {{number_format($total_total, 2)}} </span></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-3 mb-6 col-xs-6">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Total Savings</div>
                                        <div class="panel-title p-2"><span class="text-bold">NGN {{number_format($total_saving, 2)}}</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-no-gutters">
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Total Shares</div>
                                        <div class="panel-title p-2"><span class="text-bold">NGN {{number_format($total_share, 2)}} </span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Total Paid Loans</div>
                                        <div class="panel-title p-2"><span class="text-bold">NGN {{number_format($total_loan, 2)}}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-capitalize">Admin & Others</div>
                                        <div class="panel-title p-2"><span class="text-bold">NGN {{number_format($total_admin + $total_others, 2)}}</span></div>
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
                                        <div class="panel-title p-2"><span class="text-bold">{{ date('d-M-Y',strtotime($beginning_date)) }}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4 mb-2 col-xs-4 col-no-gutters">
                                    <div class="rounded">
                                        <div class="p-2 ">To date</div>
                                        <div class="panel-title p-2"><span class="text-bold">{{ date('d-M-Y',strtotime($ending_date)) }}</span></div>
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
                        <th>ID</th>
                        <th>T.Amount(#)</th>
                        <th>T.Savings(#)</th>
                        <th>T.Shares(#)</th>
                        <th>Paid Loans(#)</th>
                        <th>T.Others(#)</th>
                        <th>T.Admin(#)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ledgers as $ledger)
                        <tr wire:key="item-generalledger-{{ $ledger->id }}">
                            <td>{{ date('d-M-Y',strtotime($beginning_date)) }} | {{date('d-M-Y', strtotime($ending_date))}}</td>    
                            <td>{{ $ledger->coopId }}</td>
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

            <p><h2>Special Savings Total</h2></p>

            <table class="table table-striped table-bordered table-responsive">
                <thead>
                    <tr class="active">
                        <th>Date</th>
                        <th>ID</th>
                        <th>T.Hajj(#)</th>
                        <th>T.Ileya(#)</th>
                        <th>T.School-Fees(#)</th>
                        <th>T.Kids(#)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ledgers as $ledger)
                        <tr wire:key="item-generalledger-{{ $ledger->id }}">
                            <td>{{ date('d-M-Y',strtotime($beginning_date)) }} | {{date('d-M-Y', strtotime($ending_date))}}</td>    
                            <td>{{ $ledger->coopId }}</td>
                            <td>{{ number_format($ledger->hajj, 2) }}</td>
                            <td>{{ number_format($ledger->ileya, 2) }}</td>
                            <td>{{ number_format($ledger->school, 2) }}</td>
                            <td>{{ number_format($ledger->kids, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        <!-- </div> -->
    </body>
</html>