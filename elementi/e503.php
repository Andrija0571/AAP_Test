<?php if (!isset($provjera)){header('Location: https://www.recepcija.hr');die; }     ?>
<?php 
header('HTTP/2.0 '.$type.' '.$desc);
$_GET['e']=$type;
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<head>
    <title><?php echo $type. " ". $desc;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="Generator" content="XPect Pro 9.0">
    <meta name="Description" content="Custom Error Page">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="-1">
	<meta name="robots" content="noindex, nofollow" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,800" rel="stylesheet">
    <style>
    body {font-family: 'Open Sans', sans-serif; font-weight:800;color:#616161;}
    .error {position:absolute;left:0px;top:80px;width:100%;text-align:center;font-size:18px;}
    .type {position:absolute;left:0px;top:92px;width:100%;text-align:center;font-size:112px;}
    .reason {position:absolute;left:0px;top:260px;width:100%;text-align:center;font-size:22px;}
    .link {position:absolute;left:0px;top:410px;width:100%;text-align:center;}
    </style>
</head>
<body>
<div class="error">Greška</div>
<div class="type">
<?php echo $type; ?>
</div>
<div class="reason">
<?php echo $reason; ?>
</div>
<div class="link">
<a href="<?php echo $linkaddr;?>">
<?php echo $link; ?>
</a>
</div>
</body>
</html>