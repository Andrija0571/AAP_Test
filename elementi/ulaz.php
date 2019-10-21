<?php 
//--captcha mockup, user must pick named color--//
$eDiv="<div class=\"login_color_item\" onClick=\"setForm('?COLOR_NAME');\" style=\"background-color:?COLOR;\">&nbsp;</div>";
$eDivR="<div class=\"login_space\">&nbsp;</div>";
$colorPicker="";
$glavni->ShufflePreserving($eColors);
foreach ($eColors as $color) {
	$c=str_replace("?COLOR_NAME",$color,$eDiv);
	$c=str_replace("?COLOR",array_search($color,$eColors),$c);
	$colorPicker.=$c.$eDivR;
	}
?>
<!-- Login -->			  
<section class="faktor_login relative">
	<div class="container">
		<div class="row h-100 justify-content-center align-items-center">
			<div class="col-md-8">
			<!-- these elements are translated if a language cookie from previous session exists -->
				<h3>Prijava za ulaz u korisničke stranice</h3> 
				<!-- As required by test (I have different approach) -->
				<?php if ($password_failed) echo "<h4>Korisničko ime/lozinka je neispravna. Molim pokušajte ponovo</h4>"; ?>
				<form name="frm" method="post" autocomplete = "off">
					<div class="col-md-12 mt-5">
						<div class="form-group">
							<input type="text" class="form-control login_field" id="korisnik" name="korisnik" placeholder="Korisničko ime (12345 za ulaz)" required onBlur="Verify('korisnik');">
							<input type="password" class="form-control login_field" id="lozinka" name="lozinka"   placeholder="Lozinka" required  onBlur="Verify('lozinka');">
							<div class="col-md-12 login_color" id="login_color">
								Provjera pristupa - kliknite na <?php echo $oneColor; ?> polje:<br>
								<div class="pt-2"><?php echo $colorPicker; ?></div>	
							</div>
							<button type="Button" class="btn btn-green" onClick="sendForm();">Ulaz</button>
						</div>
					</div>
					<!-- token is set as cookie and its hash against color is sent in post field -->
					<input type="hidden" id="test" name="test" value="-">
				</form>
			</div>
		</div>
	</div>
</section>

<!-- JScript -->
<script src="jscript/jquery-2.2.4.min.js"></script>
<script src="jscript/bootstrap.min.js"></script>	
<script src="jscript/main.js"></script>
<script language="Javascript">
var loz='-';
var ime='-';
function sendForm()
{
	//usually there is a minimal length for password
	if ($('#korisnik').val().length<3 || $('#lozinka').val().length<3)
		return false;
	$('#test').val($('#korisnik').val());
	$('#korisnik').val(ime);
	$('#lozinka').val(loz);
	document.frm.action='check.php?c=<?php echo "token_parts_removed_in_this_test"; ?>';
	document.frm.submit();
}
function setForm(clr)
{
	ime=Hash($('#korisnik').val()+clr+'<?php echo $customerid;?>','<?php echo $customer;?>');
	loz=Hash($('#korisnik').val()+'x'+$('#lozinka').val()+'<?php echo $customerid;?>','<?php echo $customer;?>');
	//set hidden field with token / selected color
	$('#login_color').addClass('login_hide');//hide color selector
}
//http://pajhome.org.uk/crypt/md5 
function Hash(d, k){ return rstr2hex(rstr_hmac_sha256(str2rstr_utf8(k), str2rstr_utf8(d))); }function rstr_hmac_sha256(key, data){  var bkey = rstr2binb(key);  if(bkey.length > 16) bkey = binb_sha256(bkey, key.length * 8);  var ipad = Array(16), opad = Array(16);  for(var i = 0; i < 16; i++)  {    ipad[i] = bkey[i] ^ 0x36363636;    opad[i] = bkey[i] ^ 0x5C5C5C5C;  }  var hash = binb_sha256(ipad.concat(rstr2binb(data)), 512 + data.length * 8);  return binb2rstr(binb_sha256(opad.concat(hash), 512 + 256));} function rstr2hex(input){    var hex_tab =   "0123456789abcdef"  ;  var output = "";  var x;  for(var i = 0; i < input.length; i++)  {    x = input.charCodeAt(i);    output += hex_tab.charAt((x >>> 4) & 0x0F)           +  hex_tab.charAt( x        & 0x0F);  }  return output;}    function str2rstr_utf8(input){  var output = "";  var i = -1;  var x, y;  while(++i < input.length)  {        x = input.charCodeAt(i);    y = i + 1 < input.length ? input.charCodeAt(i + 1) : 0;    if(0xD800 <= x && x <= 0xDBFF && 0xDC00 <= y && y <= 0xDFFF)    {      x = 0x10000 + ((x & 0x03FF) << 10) + (y & 0x03FF);      i++;    }        if(x <= 0x7F)      output += String.fromCharCode(x);    else if(x <= 0x7FF)      output += String.fromCharCode(0xC0 | ((x >>> 6 ) & 0x1F),                                    0x80 | ( x         & 0x3F));    else if(x <= 0xFFFF)      output += String.fromCharCode(0xE0 | ((x >>> 12) & 0x0F),                                    0x80 | ((x >>> 6 ) & 0x3F),                                    0x80 | ( x         & 0x3F));    else if(x <= 0x1FFFFF)      output += String.fromCharCode(0xF0 | ((x >>> 18) & 0x07),                                    0x80 | ((x >>> 12) & 0x3F),                                    0x80 | ((x >>> 6 ) & 0x3F),                                    0x80 | ( x         & 0x3F));  }  return output;}    function rstr2binb(input){  var output = Array(input.length >> 2);  for(var i = 0; i < output.length; i++)    output[i] = 0;  for(var i = 0; i < input.length * 8; i += 8)    output[i>>5] |= (input.charCodeAt(i / 8) & 0xFF) << (24 - i % 32);  return output;} function binb2rstr(input){  var output = "";  for(var i = 0; i < input.length * 32; i += 8)    output += String.fromCharCode((input[i>>5] >>> (24 - i % 32)) & 0xFF);  return output;} function sha256_S (X, n) {return ( X >>> n ) | (X << (32 - n));}function sha256_R (X, n) {return ( X >>> n );}function sha256_Ch(x, y, z) {return ((x & y) ^ ((~x) & z));}function sha256_Maj(x, y, z) {return ((x & y) ^ (x & z) ^ (y & z));}function sha256_Sigma0256(x) {return (sha256_S(x, 2) ^ sha256_S(x, 13) ^ sha256_S(x, 22));}function sha256_Sigma1256(x) {return (sha256_S(x, 6) ^ sha256_S(x, 11) ^ sha256_S(x, 25));}function sha256_Gamma0256(x) {return (sha256_S(x, 7) ^ sha256_S(x, 18) ^ sha256_R(x, 3));}function sha256_Gamma1256(x) {return (sha256_S(x, 17) ^ sha256_S(x, 19) ^ sha256_R(x, 10));}function sha256_Sigma0512(x) {return (sha256_S(x, 28) ^ sha256_S(x, 34) ^ sha256_S(x, 39));}function sha256_Sigma1512(x) {return (sha256_S(x, 14) ^ sha256_S(x, 18) ^ sha256_S(x, 41));}function sha256_Gamma0512(x) {return (sha256_S(x, 1)  ^ sha256_S(x, 8) ^ sha256_R(x, 7));}function sha256_Gamma1512(x) {return (sha256_S(x, 19) ^ sha256_S(x, 61) ^ sha256_R(x, 6));}var sha256_K = new Array(  1116352408, 1899447441, -1245643825, -373957723, 961987163, 1508970993,  -1841331548, -1424204075, -670586216, 310598401, 607225278, 1426881987,  1925078388, -2132889090, -1680079193, -1046744716, -459576895, -272742522,  264347078, 604807628, 770255983, 1249150122, 1555081692, 1996064986,  -1740746414, -1473132947, -1341970488, -1084653625, -958395405, -710438585,  113926993, 338241895, 666307205, 773529912, 1294757372, 1396182291,  1695183700, 1986661051, -2117940946, -1838011259, -1564481375, -1474664885,  -1035236496, -949202525, -778901479, -694614492, -200395387, 275423344,  430227734, 506948616, 659060556, 883997877, 958139571, 1322822218,  1537002063, 1747873779, 1955562222, 2024104815, -2067236844, -1933114872,  -1866530822, -1538233109, -1090935817, -965641998);function binb_sha256(m, l){  var HASH = new Array(1779033703, -1150833019, 1013904242, -1521486534,                       1359893119, -1694144372, 528734635, 1541459225);  var W = new Array(64);  var a, b, c, d, e, f, g, h;  var i, j, T1, T2;   m[l >> 5] |= 0x80 << (24 - l % 32);  m[((l + 64 >> 9) << 4) + 15] = l;  for(i = 0; i < m.length; i += 16)  {    a = HASH[0];    b = HASH[1];    c = HASH[2];    d = HASH[3];    e = HASH[4];    f = HASH[5];    g = HASH[6];    h = HASH[7];    for(j = 0; j < 64; j++)    {      if (j < 16) W[j] = m[j + i];      else W[j] = safe_add(safe_add(safe_add(sha256_Gamma1256(W[j - 2]), W[j - 7]),                                            sha256_Gamma0256(W[j - 15])), W[j - 16]);      T1 = safe_add(safe_add(safe_add(safe_add(h, sha256_Sigma1256(e)), sha256_Ch(e, f, g)),                                                          sha256_K[j]), W[j]);      T2 = safe_add(sha256_Sigma0256(a), sha256_Maj(a, b, c));      h = g;      g = f;      f = e;      e = safe_add(d, T1);      d = c;      c = b;      b = a;      a = safe_add(T1, T2);    }    HASH[0] = safe_add(a, HASH[0]);    HASH[1] = safe_add(b, HASH[1]);    HASH[2] = safe_add(c, HASH[2]);    HASH[3] = safe_add(d, HASH[3]);    HASH[4] = safe_add(e, HASH[4]);    HASH[5] = safe_add(f, HASH[5]);    HASH[6] = safe_add(g, HASH[6]);    HASH[7] = safe_add(h, HASH[7]);  }  return HASH;}function safe_add (x, y){var lsw = (x & 0xFFFF) + (y & 0xFFFF);  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);  return (msw << 16) | (lsw & 0xFFFF);} 
</script>
