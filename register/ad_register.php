<!DOCTYPE html> 
<head>
<script>
function Search(search) {
var arrSearchResult = [];
var strSearch = search;

strDomain="DC=usda,DC=net";
strOU = "OU=ENDUSERS,OU=_FOREST_SERVICE,OU=FS,OU=Agencies"; // Set the OU to search here.
strAttrib = "samaccountname,givenName,sn,userPrincipalName,telephoneNumber,mail"; // Set the attributes to retrieve here.

// Browser connection to active directory.
objConnection = new ActiveXObject("ADODB.Connection");
objConnection.Provider="ADsDSOObject";
objConnection.Open("ADs Provider");
objCommand = new ActiveXObject("ADODB.Command");
objCommand.ActiveConnection = objConnection;
var Dom = "LDAP://"+strOU+","+strDomain;
var arrAttrib = strAttrib.split(",");

document.getElementById("domain").innerHTML=Dom;

objCommand.CommandText = "select '"+strAttrib+"' from '"+Dom+
  "' WHERE objectCategory = 'user' AND objectClass='user' AND "+
  "userPrincipalName='"+search+"' ORDER BY samaccountname ASC";

document.getElementById("cmdtxt").innerHTML=objCommand.CommandText;

try {

  objRecordSet = objCommand.Execute();

  document.getElementById("numrecords").innerHTML=objRecordSet.RecordCount ;

  objRecordSet.Movefirst;
  while(!(objRecordSet.EoF)) {
    var locarray = new Array();
    for(var y = 0; y < arrAttrib.length; y++) { 
      locarray.push(objRecordSet.Fields(y).value); 
    } 
    arrSearchResult.push(locarray); 
    objRecordSet.MoveNext; 
  } 
  return arrSearchResult; 
} catch(e) { alert(e.message); } 
} 
</script>
</head>
<body>

<h1>My Web Page</h1>

<ol>
<li id="domain">domain</li>
<li id="cmdtxt">query text</li>
<li id="numrecords"># records</li>
</ol>

<p id="demo">A Paragraph</p>

<button type="button" 
        onclick="document.getElementById('demo').innerHTML=Search(
	'<?php $_SERVER['SSL_CLIENT_S_DN'] ?>')">Find <?php $_SERVER['SSL_CLIENT_S_DN']?></button>

</body>

