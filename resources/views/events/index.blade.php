<!DOCTYPE html>
<html>
  <head>
    <title>Google Calendar Event Manage</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="{{ asset('/css/calendar.css') }}" rel="stylesheet">
    <script src="{{ asset('/js/calendar.js') }}"></script>
  </head>
  <body>
    <div class="container">
<center>

  <?php if(isset($_SESSION['status']) && !empty($_SESSION['status'])){     
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    '.$_SESSION["status"].'
    </div>';
    $_SESSION["status"]='';
  }
  ?>


</center>
    <div id="page-body">
    <!-- [PERIOD SELECTOR] -->
    <div id="cal-date">
      <select id="cal-mth"></select>
      <select id="cal-yr"></select>
      <input id="cal-set" type="button" value="Show"/>
    </div>

    <!-- [CALENDAR] -->
    <div id="cal-container"></div>

    <!-- [EVENT] -->
    <div id="cal-event"></div>


  </div>
</div>
@include('sweetalert::alert')
</body>
</html>

