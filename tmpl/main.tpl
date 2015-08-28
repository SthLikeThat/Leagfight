<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>%title%</title>
	<link rel="shortcut icon" href="../images/icons/%shortcut_icon%.png" type="image/png">
	<link href="css/main_style.css" rel="stylesheet">
	
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/bootstrap_my.css" rel="stylesheet">
	<link href="css/font-awesome.css" rel="stylesheet">

</head>

<body>
    <span id="tooltip"></span>
	
	<div class="navbar navbar-inverse navbar-static-top leag-navbar">
		<div class="container">
            %menu%
		</div>
	</div>
	
	<div class="main-content">
        <div class="full-container navbar navbar-default" style="background: #A0C9A0;">
			%header%
		</div>
		
		<div class="full-container" style="padding:2px;">
			%center%
		</div>
    </div>

	
    <footer class="footer">
        Какая то инфа
    </footer>
    <div class="modal fade in" id="alert_danger">
      <div class="modal-dialog modal-md " style="margin-top: 10%;">
          <div class="modal-content">
              <div class="modal-header alert-danger">
                 <button class="close" type="button" onclick="$('#alert_danger').hide();">
                      <i class="glyphicon glyphicon-remove"></i>
                  </button>
                  <h4 class="modal-title">Что-то пошло не так</h4>
              </div>
              <div class="modal-body">
                   
              </div>
          </div>
      </div>
    </div>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/scripta.js"></script>
</body>

</html>