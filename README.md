# AAP_Test
AAP Test\
A simplified version of my current login module\
Notes: \
a) there is no password confirmation field (it is never used in login screens, only in password change screens) \
b) data in login forms is never sent in plain form but as hashes and cannot be reloaded\
    (unless ajax is used or data is stored in local storage) \
c) I have added return to index.php but in general prefer not to allow fast response to failed login attempt\
d) form error checking is demonstrated with input value length<3 \
e) password match (for two fields) could be done: \
	$(#input1).val()!=$(#input2).val() \
Since there is no database in backend, username '12345' (with any password) will pass, any other input will return error\
There is also a Captcha mockup in form of color picker: you must click on any color to proceed
