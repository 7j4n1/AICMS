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

		<!-- Theme Custom -->
		<script src="{{ asset('js/custom.js') }}"></script>


	</body>
</html>