<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="fixed sidebar-light">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title>{{ $title ?? "Admin Dashboard" }} | Al-Birru</title>

		<meta name="keywords" content="cooperative, islamic, society, Al-birru" />
		<meta name="description" content="Al-Birr Islamic Cooperative Multipurpose society">
		<meta name="author" content="albirr">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.css') }}" />
		<link rel="stylesheet" href="{{ asset('vendor/animate/animate.compat.css') }}">
		<link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/all.min.css') }}" />
		<link rel="stylesheet" href="{{ asset('vendor/boxicons/css/boxicons.min.css') }}" />
		<link rel="stylesheet" href="{{ asset('vendor/magnific-popup/magnific-popup.css') }}" />
		<link rel="stylesheet" href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css') }}" />
		<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.css') }}" />
		<link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap-theme/select2-bootstrap.min.css') }}" />
		<link rel="stylesheet" href="{{ asset('vendor/pnotify/pnotify.custom.css') }}" />

		<!-- Theme CSS -->
		<link rel="stylesheet" href="{{ asset('css/theme.css') }}" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="{{ asset('css/skins/default.css') }}" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="{{ asset('css/custom.css') }}">

		@livewireStyles
        @stack('page-styles')

		<!-- Head Libs -->
		<script src="{{ asset('vendor/modernizr/modernizr.js') }}"></script>

	</head>
	<body class="{{ $bodyClass ?? "" }}">
        
        @yield('content')
		
        @stack('page-scripts')

		<!-- Theme Base, Components and Settings -->
		<script src="{{ asset('js/theme.js') }}"></script>

		<!-- Theme Initialization Files -->
		<script src="{{ asset('js/theme.init.js') }}"></script>

		<script>
			function initTable(params) {
            	(function($) {

					'use strict';

					var datatableInit = function() {
						var $table = $('#datatable-tabletools');

						var $table = $table.dataTable({
							sDom: '<"text-right mb-md"T><"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p',
							buttons: [
								{
									extend: 'print',
									text: 'Print',
								},
								{
									extend: 'excel',
									text: 'Excel',
								},
								{
									extend: 'pdf',
									text: 'PDF',
									exportOptions: {
										columns: params
									},
									customize : function(doc){
										var colCount = new Array();
										$('#datatable-tabletools').find('tbody tr:first-child td').each(function(){
											if($(this).attr('colspan')){
												for(var i=1;i<=$(this).attr('colspan');$i++){
													colCount.push('*');
												}
											}else{ colCount.push('*'); }
										});
										doc.content[1].table.widths = colCount;
									}
										
								},
							]
						});

						$('<div />').addClass('dt-buttons mb-2 pb-1 text-end').prependTo('#datatable-tabletools_wrapper');

						$table.DataTable().buttons().container().prependTo( '#datatable-tabletools_wrapper .dt-buttons' );

						$('#datatable-tabletools_wrapper').find('.btn-secondary').removeClass('btn-secondary').addClass('btn-default');
					};

					$(function() {
						datatableInit();
					});

					}).apply(this, [jQuery]);


					(function($) {

					'use strict';

					var datatableInit2 = function() {
						var table = $('#datatable-tabletools2');

						var table2 = table.dataTable({
							sDom: '<"text-right mb-md"T><"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p',
							buttons: [
								{
									extend: 'print',
									text: 'Print Others'
								},
								{
									extend: 'excel',
									text: 'Excel-Others'
								},
								{
									extend: 'pdf',
									text: 'PDF-Others',
									customize : function(doc){
										var colCount = new Array();
										$('#datatable-tabletools2').find('tbody tr:first-child td').each(function(){
											if($(this).attr('colspan')){
												for(var i=1;i<=$(this).attr('colspan');$i++){
													colCount.push('*');
												}
											}else{ colCount.push('*'); }
										});
										doc.content[1].table.widths = colCount;
									}
								}
							]
						});

						$('<div />').addClass('dt-buttons mb-2 pb-1 text-end').prependTo('#datatable-tabletools2_wrapper');

						table2.DataTable().buttons().container().prependTo( '#datatable-tabletools2_wrapper .dt-buttons' );

						$('#datatable-tabletools2_wrapper').find('.btn-secondary').removeClass('btn-secondary').addClass('btn-default');
					};

					$(function() {
						datatableInit2();
					});

				}).apply(this, [jQuery]);


        	}
		</script>


	</body>
</html>