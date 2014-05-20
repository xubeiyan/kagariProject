<?php
include_once('languages/setlanguage.php');
$domain = 'test';
bindtextdomain($domain, "languages/");
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);
?>
<html>
<head>
<meta http-equiv="Content-Type"  content= "text/html; charset=utf-8"  />
<title>title</title>
</head>
<body bgcolor="#FFFFFF"  text= "#000000"  link= "#FF9966"  vlink= "#FF9966"  alink="#FFCC99" >
<?php echo gettext('hello world.') ?>
</body>
</html>