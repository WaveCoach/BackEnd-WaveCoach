<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Responsive Admin Dashboard Template">
    <meta name="keywords" content="admin,dashboard">
    <meta name="author" content="stacks">
    <!-- The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title -->
    <title>Circl - Responsive Admin Dashboard Template</title>

    <!-- Styles -->
    @include('includes.style')
    @stack('custom-style')


</head>

<body>

    <div class="page-container">
        <div class="page-header">
            {{-- navbar  --}}
            @include('includes.navbar')
        </div>
        <div class="page-sidebar">
            {{-- sidebar  --}}
            @include('includes.sidebar')
        </div>
        <div class="page-content">
            {{-- main  --}}
            <div class="main-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Javascripts -->
    @include('includes.script')
    @stack('custom-scripts')
</body>

</html>
