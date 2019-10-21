function Verify(s) 
{
	Parse(s);
	s='#'+s;
	if ($(s).val().length<3)//usually there is a minimal length for password (and username)
	{
		$(s).val()=='';
		if (!$(s).hasClass('form_error'))
			$(s).addClass('form_error');//mark field red
		return;
	}
	if ($(s).hasClass('form_error'))
		$(s).removeClass('form_error'); //field is ok, remove red
	return;
}
function Parse(s)
{	
	//sample parse, without international chars
	//not really needed as hashes are sent
	var r, re;
	re = /[^A-Za-z0-9._ČćŠšĐđŽžČč@+-:\/()\s]/g;
	r = document.getElementById(s).value.replace(re, "");	
	document.getElementById(s).value=r;
}