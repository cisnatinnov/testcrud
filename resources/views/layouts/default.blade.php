<!doctype html>
<html>
<head>
  @include('includes.head')
</head>
<body class="skin-black">
<div class="container">
   <header class="header">
      @include('includes.header')
   </header>
   <div class="wrapper row-offcanvas row-offcanvas-left">
      @include('includes.sidebar')
      <aside class="right-side">
         @yield('content')
      </aside>
   </div>
   @include('includes.footer')
</div>
</body>
</html>