<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OAKE-Referral</title>
  @if (file_exists(public_path('build')))
    @vite('resources/css/app.css')
  @else
    <link rel="stylesheet" href="/css/app.css">
  @endif
</head>
<body>
  <header>
    <div class="nav">
      <div class="logo">OAKE-Referral</div>
      <div class="nav-links">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        <a href="{{ route('existing') }}" class="{{ request()->routeIs('existing') ? 'active' : '' }}">Existing patient</a>
        <a href="{{ route('new') }}" class="{{ request()->routeIs('new') ? 'active' : '' }}">New patient</a>
        <a href="{{ route('hospitals') }}" class="{{ request()->routeIs('hospitals') ? 'active' : '' }}">Hospitals</a>
        <a href="{{ route('insights') }}" class="{{ request()->routeIs('insights') ? 'active' : '' }}">Insights &amp; ethics</a>
      </div>
    </div>
  </header>

  <div class="wrap">
    @yield('content')
  </div>

  <footer>OAKE-Referral · CS254_B — Introduction to Artificial Intelligence</footer>
  
</body>
</html>
