<?php 

if (is_uploaded_file($_FILES['firma']['tmp_name'])) {
		if($_FILES['firma']['size'] <= 500000) {
			   if($_FILES['firma']['type']=="image/jpeg" ){
					if(!move_uploaded_file  ($_FILES['firma']['tmp_name'] , "firmas/".$_POST['username'].".jpg")){
						$error = "no se ha podido mover";
					}else{
					header("Location: close.php");
					}
				
				
			   }else{$error = "fomato no valido";}
		}	else{$error = "excede el tamaño";}
	}else{$error = "no envio ningun archivo";}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body> 
<form name="cambiar" method="post" enctype="multipart/form-data">
<table width="331" border="0" cellpadding="0" cellspacing="0" class="wrapper">
  <!--DWLayoutTable-->
  <tr>
    <td height="23" colspan="3" valign="top">Cambiar Firma</td>
    <td width="18">&nbsp;</td>
  </tr>
  <tr>
    <td width="68" height="26" valign="top" >Aarchivo:</td>
    <td colspan="2" valign="top"><label>
      <input type="file" name="firma" id="firma" />
      <input type="hidden" name="username" value="<?php echo $_GET['username'];?>" />
    </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="12"></td>
    <td width="120"></td>
    <td width="125"></td>
    <td></td>
  </tr>
  <tr>
    <td height="22"></td>
    <td></td>
    <td valign="top"><label>
      <input type="submit" name="button" id="button" value="Acpetar" />
    </label></td>
  <td></td>
  </tr>
</table>
</form>
</body>
</html>
