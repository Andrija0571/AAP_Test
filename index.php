<?php
//this is very simplified version of my current login module access
//note: there is no password confirmation field (it is never used in login screens, only
//in password change screens) - data in login forms is never sent in plain form but as hashes and cannot
//be reloaded (unless ajax is used or data is stored in local storage)
//since I always assume web app will be attacked, I prefer not to allow fast response to failed
//login attempt
//form error checking is demonstrated with input value length<3, same principle goes for password
//checking $(#input1).val()!=$(#input2).val()
//username 12345 will pass, any other input will not pass
//not clicking a color (any) will fail
$provjera=17;
$password_failed=false;
while (list ($key,$val) = each ($_GET))
{	 
	switch ($key)
	{			
		case "status": $password_failed=true;break;//for test only
	}
}
include ("elementi/glavni.php");
$glavni=new Glavni();
//customer is hashed result based on customer link (customer is company renting an app)
$customer="abcdef123456";
//customerid is link that has accessed this page (each customer has it's own subdirectory, ie www.faktor.hr/mycompany which in turn links to this file - for all customers)
$customerid="link";
$local=$glavni->CleanInputNumeric($_SERVER["REMOTE_ADDR"]);
$ip=hash("md5",$local);
//pick a color (captcha mockup)
$eColors=array("#F52301"=>"crveno","#FFAAFF"=>"ružičasto","#01F535"=>"svijetlozeleno","#F5D501"=>"žuto","#2D68E1"=>"plavo","#2B2B2B"=>"crno","#97999D"=>"sivo","#00A41F"=>"tamnozeleno");
$oneColor=$eColors[array_rand($eColors)];
//insert token with selected color into db; token will be used to get hash from user input
$token=hash_hmac("sha256",uniqid().$local.$customer,$oneColor);
$sql="INSERT logbook (id,status,ip,kod) VALUES('" . $token ."',0,'".$ip."','".$oneColor."')";
$glavni->OpenConnection();
$glavni->UpdateDB($sql);//no error handling in this test: failure to access/update db=error page
$glavni->CloseConnection();

require("elementi/zaglavlje.php");
require("elementi/logo.php");
require("elementi/ulaz.php");
?>

	</body>
</html>