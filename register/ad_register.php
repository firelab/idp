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

<?php 
    $subject = $_SERVER['SSL_CLIENT_S_DN'] ;
    $sub_re = '/UID=(\d+)\+CN=([[:alnum:]\.\s]+),OU=Department of Agriculture,O=U.S. Government,C=US/';
    preg_match($sub_re, $subject, $matches) ;
    $name_on_card = $matches[2];
    $fedidcard = $matches[1] . '@fedidcard.gov';
?>
<title>Registration for <?php echo $name_on_card; ?></title>
</head>
<body>

<h2>Active Directory information about <?php echo $name_on_card; ?></h2>
<ol>
<li>Card ID #: <?php echo $fedidcard; ?></li>
<li id="domain">domain</li>
<li id="cmdtxt">query text</li>
<li id="numrecords"># records</li>
</ol>

<p id="demo">A Paragraph</p>

<button type="button" 
        onclick="document.getElementById('demo').innerHTML=Search(
	'<?php echo $fedidcard; ?>')">Find <?php echo $name_on_card; ?></button>

</body>

