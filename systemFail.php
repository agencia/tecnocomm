<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sistema Tecnocomm</title>
<script type="text/javascript">
    <!--
    if (top.frames.length > 0) {
        top.location.href = location.href;
    }
    // -->
</script>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
</style>
</head>

<body  <?php if(isset($_GET['access']))
				if($_GET['access']=1) echo " onload=\"javascript:window.close()\"" ; ?> class="wrapper" >
<p align="center"><img src="LOGO.jpg" width="333" height="75" align="middle" /></p>
<p align="center">&nbsp;</p>
<p align="center"><img src="images/AdmnistracionAcceso.png" width="48" height="48" /></p>
<p align="center">Su sesi&oacute;n ha terminado por inactividad o sus datos son incorrectos.<br />
  <a href="index.php">VUELVA A INTENTARLO</a></p>
</body>
</html>
