<?php if (!isset($provjera)){header('Location: http://www.recepcija.hr');die; } ?>

<?php
class Glavni
{

//DATABASE--------------------
	function OpenConnection()
	{
		return true;
	}
	function OpenDBConnection($u,$p,$d)//proper call
	{
		global $baza;
		$baza = new mysqli("localhost",$u,$p,$d);
		if (!$baza)
			return false;
		$baza->query("SET NAMES 'utf8'");
		return true;
	}
	function CloseConnection()
	{
		//global $baza;
		//$baza->close();
	}
	function ReadData($sql)
	{
		$resp=array("1","2","3","4");
		//global $baza;
		//$result=$baza->query($sql);
		//if ($result)
		//{
		//	while($row = $result->fetch_array())
		//	{
		//		for ($j=0;$j<count($row)/2;$j++)
		//					$resp[]=$row[$j];
		//	}
		//	$result->close();
		//}
		//else
		//	$this->LogQueryError($sql,$baza->error,"READ");
		return $resp;
	}
	function ReadRow($sql)
	{
		$row=array("1","2","3");//for test
		//global $baza;
		//$result=$baza->query($sql);
		//if ($result)
		//{	
		//	$row = $result->fetch_row();
		//	$result->close(); 
		//}
		//else
		//	$this->LogQueryError($sql,$baza->error,"READ");
		return $row; 
	}
	function ExecuteScalar($sql)
	{
		global $baza;
		$result=$baza->query($sql);
		if (!$result)
			return "";
		$row = $result->fetch_row();
		$result->close(); 
		return $row[0];
	}
	function UpdateDB($sql)
	{
		//global $baza;
		//$result=$baza->query($sql);
		//if (!$result)
		//	$this->LogQueryError($sql,$baza->error,"UPDATE");
		//else
		//	$this->LogQuery($sql);//I keep transaction records on all desktop and web applications in a
								  //daily log - both to be able to rebuild database and to find (if needed)
								  //who did what and when
		//return $result;
	}


//INPUT CLEAN--------------
	function CleanInputStrict($text)
	{
		$text=trim($text);
		$text=preg_replace('/\s\s+/',' ',$text);
		$text = preg_replace("![^A-Za-z0-9.=_?@%+-:\/()\s\[\]]+!", "", $text );
		return $text;
	}
 	function CleanInput($text)
	{
		$text=trim($text);
		$text=preg_replace('/\s\s+/',' ',$text);
		$text=stripslashes($text);
		$text=htmlspecialchars($text);
		return $text;
	}
	function CleanInputNumeric($number)
	{
		$number=trim($number);
		$number=preg_replace("![^0-9,.-:]+!", "", $number );
		return $number;
	}
	function CleanSQLInput($text)
	{
		$text=trim($text);
		$text=stripslashes($text);
		$text=htmlspecialchars($text);
		global $baza;
		$text=$baza->real_escape_string($text);
		return $text;
	}
//USER PWD GENERATOR--------------
	function MakeHash($pwd,$user,$customerid,$customer)
	{
		$result=hash_hmac("sha256",$pwd."x".$user.$customerid,$customer);
		return $result;
	}

//PAGE TRANSFERS--------------
	function EndPage($main)
	{
		switch ($main)
		{
			case "moduli":
				$file="elementi/e502.php";
				echo file_get_contents($file);
				break;
			default:
				//actually redirects away as it would be illegal call
				header('Location: index.php?status=failed');
				break;
		}
		die;
	}
	function ErrorPage($folder,$cust,$fail,$main)
	{
		if ($main=='moduli')
		{
			$file="glavni/elementi/e502.php";
			echo file_get_contents($file);
			die;
		}
		$linkaddr="http://www.faktor.hr";
		if (strlen($folder)>2)
			$linkaddr.="/".$folder."/?index.php";
		switch ($fail)
		{
			case 1://without get
			case 2://wrong get length
			case 3://wrong get
			default:
				$reason="Traženi URL nije pronađen na serveru";
				$link="https://www.recepcija.hr";
				$desc="Not Found";
				$type="404";
				break;
			case 4://too many ids
				$reason="Usluga vam nije dostupna zbog<br>višestrukih neuspješnih prijava u sustav";
				$link="Ukoliko smatrate da se radi o pogrešci, molimo<br>kontaktirajte vašeg administratora";
				$desc="Service Unavailable";
				$type="503";
				break;
			case 5://invalid username/password
				$reason="Pogrešno korisničko ime/lozinka";
				$link="Molimo pokušajte ponovo ili <br>kontaktirajte vašeg administratora";
				$desc="Unauthorized";
				$type="401";$provjera=18;
				break;
			case 6://could not connect to db or failed to read data
				$reason="Program ne može pristupiti bazi podataka";
				$link="Molimo pokušajte ponovo ili <br>kontaktirajte vašeg administratora";
				$desc="Service Unavailable";
				$type="503";$provjera=18;
				break;
			case 7://session timeout or logged off
				$reason="Prijava istekla";
				$link="Molimo prijavite se ponovo ili <br>kontaktirajte vašeg administratora";
				$desc="Unauthorized";
				$type="401";$provjera=18;
				break;
		}
		include ("elementi/e503.php");
		die;
	}
//SESSION--------------
	function PrepareSession($user,$usercode,$auth,$customerid,$sToken,$local,$session,$globalSession,$db,$us,$cd,$progtype)
	{
		$first="bbe20b247a9?".$user."?".$usercode."?".$auth."?".$customerid."?".$sToken."?".$local."?".$session."?".$globalSession."?".$db."?".$us."?".$cd."?".$customerid."?".$progtype."?kop";
		$second=$this->EncodeText($first,hash("md5",$customerid."aOlswD".$customerid));
		$md=hash("md5",$second);
		$zip=gzcompress($second,6);
		$third=$this->ShiftBytesUp($zip);
		$fourth=base64_encode($third);
		$final=$this->ShuffleText($fourth,$md);
		$final=strtr($final,'+/','[]');
		return $final;
	}
	function SetSession($token,$usercode,$local,$session,$globalSession)
	{	
		$d=($_SERVER['HTTP_HOST']!='localhost')?$_SERVER['HTTP_HOST']:false;
		//cookie remove
		setcookie ("ct","",time() - 28800);
		setcookie ("pu","",time() - 28800);
		setcookie ("lo","",time() - 28800);
		setcookie ("pl","",time() - 28800);
		setcookie ("dl","",time() - 28800);
		//cookie add
		setcookie ("ct",$token, time() + 28800,'/',$d,false);
		setcookie ("pu",$usercode, time() + 28800,'/',$d,false);
		setcookie ("lo",$local, time() + 28800,'/',$d,false);
		setcookie ("pl",$session, time() + 28800,'/',$d,false);
		setcookie ("dl",$globalSession,time() + 28800,'/',$d,false);
	}
	function ReadSession($token,$customerid)
	{
		$response=Array();
		$token=strtr($token,'[]','+/');
		$fourth=$this->UnshuffleText($token);
		$md=$this->UnshuffleKey($token);
		$third=base64_decode($fourth);
		$zip=$this->ShiftBytesDown($third);
		$second=gzuncompress($zip);
		if ($md==hash("md5",$second))
		{
			$first=$this->DecodeText($second,hash("md5",$customerid."aOlswD".$customerid));
			$response=explode("?",$first); 
		}
		return $response;

	}
//ENCRYPT/DECRYPT FUNCTIONS--------------
	function EncodeText($contents,$oscilation)
	{
		//use:$final= Encode(text,key);$final=base64_encode($final);
		$deoscilation=$this->GetDeoscilation($oscilation);
		$count=strlen($contents);
		$result=$this->AddOscilationArray($oscilation);//+add osc len + ord[osc[len-1] bytes
		$max=0;
		$oscilator=0;
		do
		{
			$val = ord($contents[$max]);
			$val += ord($oscilation[$oscilator]);
			if ($val > 255)
				$val = $val - 256;
			$val += ord($deoscilation[$oscilator]);
			if ($val > 255)
				$val = $val - 256;
			$result.=chr($val);
			$oscilator++;
			if ($oscilator == strlen($oscilation) )
				$oscilator = 0;
			$max++;
		}
		while ($max<$count);
		return $result;
	}
	function DecodeText($contents,$oscilation)
	{
		//use $final=base64_decode($final);$n=Decode(text,key);
		$deoscilation=$this->GetDeoscilation($oscilation);
		$count=strlen($contents);
		$max=192+strlen($oscilation)+ord($oscilation[strlen($oscilation)-2]);
		$result="";
		$oscilator=0;
		do
		{
			$val = ord($contents[$max]);
			$val -= ord($deoscilation[$oscilator]);
			if ($val < 0)
				$val = $val + 256;
			$val -= ord($oscilation[$oscilator]);
			if ($val < 0)
				$val = $val + 256;
			$result.=chr($val);
			$oscilator++;
			if ($oscilator == strlen($oscilation) )
				$oscilator = 0;
			$max++;
		}
		while ($max<$count);
		return $result;
	}
	function AddOscilationArray($oscilation)
	{		
		$text="";
		$seed=(double)microtime()*1000003;
		if (function_exists('getmypid'))
		$seed+=getmypid();
			mt_srand($seed);
		for ($i=0;$i<192;$i++)
			$text.=chr(mt_rand(0,255));
		$c=strlen($oscilation)+ord($oscilation[strlen($oscilation)-2]);
		$seed=(double)microtime()*1000003;
		if (function_exists('getmypid'))
			$seed+=getmypid();
		mt_srand($seed);
		for ($i=0;$i<$c;$i++)
		{
			$text.=chr(mt_rand(0,255));
		}
		return $text;  
	}
	function GetDeoscilation($oscilation)
	{
		$deoscilation=$oscilation; 
		$j = 0;
		for ($i=strlen($oscilation)-1;$i>-1;$i--)
		{
			$deoscilation[$j] = $oscilation[$i]; $j++;
		}
		return $deoscilation;
	}
	function ShiftBytesUp($data)	//sample shift some bytes to hide zip header
	{								
		for ($i=0;$i<17;$i++)
		{
			$val=ord($data[$i]);
			$val+=17;//customer title chars are used for shifting 
			if ($val>255)
				$val = $val - 256;
			$data[$i]=chr($val);
		}
		for ($i=strlen($data)-21;$i<strlen($data)-4;$i++)
		{
			$val=ord($data[$i]);
			$val+=13;//customer title chars are used for shifting
			if ($val>255)
				$val = $val - 256;
			$data[$i]=chr($val);
		}
		return $data;
	}
	function ShiftBytesDown($data)	//unshift some bytes to reveal zip
	{
		for ($i=0;$i<17;$i++)
		{
			$val=ord($data[$i]);
			$val-=17;//customer title chars are used for shifting
			if ($val<0)
				$val = $val + 256;
			$data[$i]=chr($val);
		}
		for ($i=strlen($data)-21;$i<strlen($data)-4;$i++)
		{
			$val=ord($data[$i]);
			$val-=13;//customer title chars are used for shifting
			if ($val<0)
				$val = $val + 256;
			$data[$i]=chr($val);
		}
		return $data;
	}
	function ShuffleText($t,$key) //mix text with md5
	{
		$r=substr($key,0,7).substr($t,0,19).substr($key,7,11).substr($t,19,31).substr($key,18).substr($t,50);
		return $r;
	}
	function UnshuffleText($t)	//extract text
	{
		$result=substr($t,7,19).substr($t,37,31).substr($t,82);
		return $result;
	}
	function UnshuffleKey($t)	//extract key
	{
		$key=substr($t,0,7).substr($t,26,11).substr($t,68,14);
		return $key;
	}
	function ShufflePreserving(&$array) {
        $keys = array_keys($array);
        shuffle($keys);
        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }
        $array = $new;
        return true;
    }
}