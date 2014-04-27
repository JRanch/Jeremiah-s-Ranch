/**
/**
*Author: Prakash Sahu
*Mail: prakash.sahu@virginsoft.net
*URL:www.virginsoft.net
**/


function getXMLHTTP() { //fuction to return the xml http object
		var xmlhttp=false;	
		try{
			xmlhttp=new XMLHttpRequest();
		}
		catch(e)	{		
			try{			
				xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e){
				try{
				req = new ActiveXObject("Msxml2.XMLHTTP");
				}
				catch(e1){
					xmlhttp=false;
				}
			}
		}
		 	
		return xmlhttp;
    }
	

function getUser(UserId)
{
	var URL = window.location.href.split("index.php")[0];
if (UserId == "" )
	{
			document.getElementById("user-check").setAttribute("class", "user-invalid");
			document.getElementById('user-check').innerHTML="Gebruikersnaam is verplicht in te vullen.";
			document.getElementById("usernamemsg").setAttribute("class", "invalid");
	}
else
	{
   //var strURL= URL+"/components/com_user/views/register/VerifyUser.php?user="+UserId;
   var strURL=URL+"index.php?option=com_ticketmaster&controller=check&task=verify&no_html=1&user="+UserId;
   var req = getXMLHTTP();
   if (req)
   {
     req.onreadystatechange = function()
     {
      if (req.readyState == 4)
      {
	  
	 // only if “OK”
	 
	 	if (req.status == 200)
         	{
				
			   //alert(req.responseText);
			   
		 		if (req.responseText.length == 7)
					{
					
		 				document.getElementById("user-check").setAttribute("class", "user-invalid");
	    				document.getElementById('user-check').innerHTML="Gebruikersnaam reeds in gebruik..";
					}
				else
					{
					
						document.getElementById("user-check").setAttribute("class", "user-valid");
						document.getElementById('user-check').innerHTML="Gebruikersnaam kan gebruikt worden.";
						document.getElementById("usernamemsg").setAttribute("class", "invalid");
						
					}
	 	 	} 
		 else 
		 	{
   	   			alert("There was a problem while using XMLHTTP:\n" + req.statusText);
	   
	 		}
       }
	   if (req.readyState == 1)
	   		{
				document.getElementById("user-check").setAttribute("class", "loading");
				document.getElementById('user-check').innerHTML="Checking........";
			}
      }
   req.open("GET", strURL, true);
   req.send(null);
   }
 }
}

function getEmail(Email) 
{
	var URL = window.location.href.split("index.php")[0];
 if (Email == "")
 	{
			document.getElementById("user-email").setAttribute("class", "user-invalid");
			document.getElementById('user-email').innerHTML="Verplicht veld!";
			document.getElementById("emailmsg").setAttribute("class", "invalid");
	}
 else 
 	{
	//alert(Email);
	var regex=/^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
	var OK = regex.exec(Email);
	if (OK)
		{
			   	//var strURL= URL+"/components/com_user/views/register/VerifyUser.php?email="+Email;
   				var strURL=URL+"index.php?option=com_ticketmaster&controller=check&task=verify&no_html=1&email="+Email;
   				var req = getXMLHTTP();
				if (req)
				   {
					   req.onreadystatechange = function()
     						{
      							if (req.readyState == 4)
      								{
	  
	 								// only if “OK”
	 								if (req.status == 200)
         								{
											//alert(req.responseText);
											if (req.responseText.length == 7)
												{
					
										 document.getElementById("user-email").setAttribute("class", "user-invalid");
										 document.getElementById('user-email').innerHTML="Email adres is reeds geregistreerd!";
													
	    											
												}
											else
												{
											document.getElementById("user-email").setAttribute("class", "user-valid");
											document.getElementById('user-email').innerHTML="Email Adres Kan gebruikt worden";
    											
													
												}
											
										}
									}

							   if (req.readyState == 1)
	   								{
										document.getElementById("user-email").setAttribute("class", "loading");
										document.getElementById('user-email').innerHTML="Checking........";
									}

							}
									req.open("GET", strURL, true);
   									req.send(null);
				   }

		}
	else
		{
			document.getElementById("user-email").setAttribute("class", "user-invalid");
			document.getElementById('user-email').innerHTML="Gelieve een geldig email adres invoeren aub..";
			document.getElementById("emailmsg").setAttribute("class", "invalid");
		}
	}
}
function getName(Name)

{
	//regex = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&]", "i");
	if ( Name == "")
		{
			document.getElementById("user-name").setAttribute("class", "user-invalid");
			document.getElementById('user-name').innerHTML="Required field";
			document.getElementById("namemsg").setAttribute("class", "invalid");
			
		}
		else 
		{
			document.getElementById("user-name").setAttribute("class", "user-valid");
			document.getElementById('user-name').innerHTML="";
		}
	
}
function passwordStrength(password)
{
	if ( password != "" )
		{
	var desc = new Array();
	desc[0] = "Very Weak";
	desc[1] = "Weak";
	desc[2] = "Better";
	desc[3] = "Medium";
	desc[4] = "Strong";
	desc[5] = "Strongest";

	var score   = 0;

	//if password bigger than 6 give 1 point
	if (password.length < 6) 
		{
			
			document.getElementById("user-password").setAttribute("class", "user-invalid");
			document.getElementById('user-password').innerHTML="Password must be at least 6 chars long";
			document.getElementById("pwmsg").setAttribute("class", "invalid");
		}
	else
		{
			score++;
			document.getElementById("user-password").setAttribute("class", "user-valid");
			document.getElementById('user-password').innerHTML="";
			document.getElementById("pwmsg").setAttribute("class", "");
		}

	//if password has both lower and uppercase characters give 1 point	
	if ( ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) ) score++;

	//if password has at least one number give 1 point
	if (password.match(/\d+/)) score++;

	//if password has at least one special caracther give 1 point
	if ( password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) )	score++;

	//if password bigger than 12 give another 1 point
	if (password.length > 12) score++;

	 document.getElementById("passwordDescription").innerHTML = desc[score];
	 document.getElementById("passwordStrength").className = "strength" + score;
	 document.getElementById("passwordDescription").className = "desc" + score;
		}
	else 
		{
			document.getElementById("user-password").setAttribute("class", "user-invalid");
			document.getElementById('user-password').innerHTML="Required field";
		}
}

function passwordMatch(password2)
{
	var password = document.getElementById('password').value
if ( password2 != "")
	{
	if ( password == password2 )
		{
			document.getElementById("user-password2").setAttribute("class", "user-valid");
			document.getElementById('user-password2').innerHTML="";
		}
	else
		{
			document.getElementById("user-password2").setAttribute("class", "user-invalid");
			document.getElementById('user-password2').innerHTML="Passwords do not match";	
		}
	}
else 
	{
		document.getElementById("user-password2").setAttribute("class", "user-invalid");
		document.getElementById('user-password2').innerHTML="Required field";
	}
}
function clearall()
{
	document.getElementById("user-check").setAttribute("class", "");
	document.getElementById("user-email").setAttribute("class", "");
	document.getElementById("user-name").setAttribute("class", "");
	document.getElementById("passwordDescription").setAttribute("class", "");
	document.getElementById("passwordStrength").setAttribute("class", "");
	document.getElementById("user-password").setAttribute("class", "");
	document.getElementById("user-password2").setAttribute("class", "");
	document.getElementById('user-check').innerHTML="";
	document.getElementById('user-name').innerHTML="";
	document.getElementById('user-email').innerHTML="";
	document.getElementById('user-password').innerHTML="";
	document.getElementById('user-password2').innerHTML="";
	document.getElementById("passwordDescription").innerHTML="";
	document.getElementById("namemsg").setAttribute("class", "");
	document.getElementById("usernamemsg").setAttribute("class", "");
	document.getElementById("emailmsg").setAttribute("class", "");
	document.getElementById("pwmsg").setAttribute("class", "");
	document.getElementById("pw2msg").setAttribute("class", "");
	
	
}
function requiredfield()
{
	alert("hiiii");
	if ( document.getElementById("name").value == "" ) 	
		{
			document.getElementById("user-name").setAttribute("class", "user-invalid");
			document.getElementById('user-name').innerHTML="Required field";
			
		}
	if ( document.getElementById("username").value == "" )
		{
			document.getElementById("user-check").setAttribute("class", "user-invalid");
			document.getElementById('user-check').innerHTML="Required field";
				
		}
	if ( document.getElementById("email").value == "" )
		{
			document.getElementById("user-email").setAttribute("class", "user-invalid");
			document.getElementById('user-email').innerHTML="Required field";
		}
}