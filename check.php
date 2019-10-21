<?php
// in this test security elements are removed
$provjera=17;
include ("elementi/glavni.php");
$glavni=new Glavni();  
$local=$glavni->CleanInputNumeric($_SERVER["REMOTE_ADDR"]);

 
//GET
if (!isset($_SERVER["QUERY_STRING"])) $glavni->EndPage(2);   
$n=0;
while (list ($key,$val) = each ($_GET))
{	 
	//switch ($key)
	//{			
	//		case "c": if ($customerid!=$glavni->CleanInputStrict($val)) $n--;	break;
    //      case "r": if (strlen($val)>2) $n++; 								break;
    //      case "n": if (strlen($val)>2) $n++; 								break;
	//}
}
$n=3;
if ($n<2) $glavni->EndPage(3);

//FIND DB  removed
//if (!isset($customerHash) || strlen($customerHash)<30)
//	$glavni->EndPage(4);

//POST
$sToken="-";
$user="-";
$pwd="-";
$test="";
while (list ($key,$val) = each ($_POST))
{	
    switch ($key)
    {
		case "test":
			$test=$glavni->CleanInputStrict($val);
			break;
        case "korisnik":
			$user=$glavni->CleanInputStrict($val);
			break;	 
        case "lozinka":
			$pwd=$glavni->CleanInputStrict($val);
			break;	//hashed password hash_hmac("sha256",$user."x".$passwd.$customerid,$customerHash);
    //  case "ime2"://ip
	//		if ($local!=$glavni->CleanInputStrict($val)) 
	//			$glavni->EndPage(7);	
	//		break; 
    //  case "ime3":
	//	$sToken=$glavni->CleanInputStrict($val);	
	//	break;//timehash
    // case "ime4":
	//	if ($customerHash!=$glavni->CleanInputStrict($val))
	//		$glavni->EndPage(4);
	//	break;
    }
}
$sToken="1234567890123456789";
if (strlen($user)<3 || strlen($sToken)<16 || strlen($pwd)<63)  
	$glavni->EndPage(5);

//TOKEN?
$glavni->OpenConnection();
$data=$glavni->ReadRow("SELECT rbr,ip,kod FROM logbook WHERE id='".$sToken."' AND status=0");
if (count($data)!=3)// || hash("md5",$local)!=$data[1]) 
{
	$glavni->CloseConnection();
	$glavni->EndPage(8); 
}
$globalSession=$data[0];
$clr=$data[2];
//CUSTOMER DATA removed
$customerid="1111";
$customerHash="11111";

//USER DATA
$fail=1;
$users=$glavni->ReadData("SELECT sifra,naziv,lozinka,ovlast FROM korisnici WHERE aktivan=1");
if ($test=="12345")	
{
	$fail=0;
	//passed
}
else if (count($users)>3)   
{ 
    $usercode=-1;
    $auth=99;
    for ($i=0;$i<count($users);$i+=4)
    {	
		//user password is stored in database as hash value
        if ($users[$i+2]==$pwd) //if password exists, is username correct?
        {
			if ($user==hash_hmac("sha256",$users[$i+1].$clr.$customerid,$customerHash))
			{
				$user=$users[$i+1];
				$usercode=$users[$i];
				$auth=$users[$i+3];
				$fail=0;
			}
            break;
        }
    }
}
if ($fail>0)
{
    $glavni->UpdateDB("UPDATE logbook SET status=1 WHERE rbr=".$globalSession);
    $glavni->CloseConnection();
    //$glavni->LogAccessError("A",$local,"Wrong user/pwd for ".$customerid);
    //$glavni->ErrorPage($customerid,$customerid,5,"main");
	header("Location:index.php?status=failed");//I would not use this as I prefer requiring user to click to return
	die;
}
//0=log attempt,1=canceled,2=log in,3=log off,4=forced log off
//remove any live token for current user
function for_test_to_show_what_actually_happens()
{
	//$glavni->current_user=$usercode;
	//$glavni->UpdateDB("UPDATE logbook SET status=4,log_out='".date("Y-m-d G:i:s")."' WHERE usercode=".$usercode." AND status<3");//forced log off
	//$glavni->UpdateDB("UPDATE logbook SET status=2,usercode=". $usercode .",auth=".$auth." WHERE id='".$sToken."'");
	//$glavni->LogAccess($local,$user,true);//always log login/logout in db and in html file
	//$session=$globalSession;
	//$token=$glavni->PrepareSession($user,$usercode,$auth,$customerid,$sToken,$local,$session,$globalSession,$customerHash,$pid,$customerDesc,$progId); //token is modified
	//$glavni->SetSession(substr($token,260),$usercode,$local,$session,$globalSession); 
	//header("Location:../".$progmap."/pocetna.php?nr=".substr($token,0,256)."&na=".$customerId."&ap=".substr($token,256,4));
}
header("Location:success.php");
die;
?>
