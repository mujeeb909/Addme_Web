<html>

<head>
  <title>{{config("app.name", "")}}</title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <meta http-equiv="Content-Language" content="" />
</head>

<body>
  <section class="container theme-margin">
    <div class="row">
      <div class="center-text full-width">
        <button type="button" style="padding: 0;border: 0;"><img src="{{ uploads_url() . 'img/addmee-logo.png' }}" alt="AddMee" width="150"> </button>
      </div>
    </div>
    <div class="row center-text">
      <h1 class="theme-color center-text full-width">Hi {{$username}},</h1>
    </div>
    <div class="row">
      <p class="center-text full-width"> We received a request to reset your password. <br><br>Your code is: {{ $otp }}. <br /><br />
        Regards<br />
        {{config("app.name", "")}}
      </p>
    </div>
  </section>
</body>

</html>