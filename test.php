<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" type="text/javascript">

function change(name){
s = document.getElementById(name);
obj = document.createElement('input')
obj.type = 'text'
obj.id = name;
obj.name = name;
document.forms[0].replaceChild(obj,s)
}



</script>
</head>

<body>
<form name="holahola" method="get">
<select name="garantia" id="garantia">
<option > 1 mes </option> 
<option>25 Anos </option>
<option onclick="change('garantia');">Otro...</option>
</select>
<input type="submit" />
</form>
</body>
</html>