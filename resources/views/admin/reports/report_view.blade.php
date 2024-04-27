<!DOCTYPE html>
<html lang="{{  str_replace('_', '-', app()->getLocale())  }}" class="fixed" >
	<head>

		<!-- Basic -->
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
		<!-- <meta charset="Unicode"> -->

		<title>{{--$memberId->surname--}} Ledger  "Admin Dashboard" | Al-Birru</title>

		<meta name="keywords" content="cooperative, islamic, society, Al-birru" />
		<meta name="description" content="Al-Birr Islamic Cooperative Multipurpose society">
		<meta name="author" content="albirr">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<!-- <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css"> -->

		<!-- Vendor CSS -->
        @include('components.layouts.partials.styles')
        @include('components.layouts.partials.jquery')
        @include('components.layouts.partials.bootstrapjs')
        @include('components.layouts.partials.fontawesome')
        

	</head>
	<body>
        <!-- Start: Main -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-sm-12 mb-4">
                    <div class="panel" style="margin-top: 10px;">
                        <div class="panel-heading">
                            <h3 class="panel-title">Account Information</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row ">
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-uppercase">Account Name</div>
                                        <div class="card-title p-2"><span class="text-bold"> {{$memberId->surname}} {{$memberId->otherNames}}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-uppercase">Total Amounts(₦)</div>
                                        <div class="card-title p-2"><span class="text-bold"> {{$dataTotals->totalAmount}} </span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-uppercase">Total Savings(₦)</div>
                                        <div class="card-title p-2"><span class="text-bold"> {{$dataTotals->savingAmount}}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-uppercase">Loan Status</div>
                                        <div class="card-title p-2"><span class="text-bold"> {{$isOnLoan ? 'Active' : 'Not Active'}}</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row ">
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-uppercase">Account Coop ID</div>
                                        <div class="card-title p-2"><span class="text-bold"> {{$memberId->coopId}}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-uppercase">Total Shares(₦)</div>
                                        <div class="card-title p-2"><span class="text-bold"> {{$dataTotals->shareAmount}} </span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-uppercase">Total Loans(₦)</div>
                                        <div class="card-title p-2"><span class="text-bold"> {{$dataTotals->loanAmount}}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mb-2 col-xs-3">
                                    <div class="rounded">
                                        <div class="p-2 text-uppercase">Admin & Others(₦)</div>
                                        <div class="card-title p-2"><span class="text-bold"> {{$dataTotals->adminCharge + $dataTotals->others}}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>