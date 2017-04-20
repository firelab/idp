<!DOCTYPE html>
<?php
require_once "userfuncs.php" ; 
?>


<head>
<title>Resetting Password for <?php echo $_SERVER["SSL_CLIENT_S_DN_CN"]?></title>
</head>
<body>
<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

// connect to ldap server
$ldapconn = ldap_connect("ldaps://ad.firelab.org/")
    or die("Could not connect to LDAP server.");
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

if ($ldapconn) {

    // binding using Kerberos credentials.
    $ldapbind = ldap_sasl_bind($ldapconn,"","","GSSAPI");

    if ($ldapbind) {
       $upn = $_SERVER["SSL_CLIENT_S_DN_UID"]."@fedidcard.gov" ; 
       $dn  = find_user_by_upn($ldapconn, $upn) ; 
       if ($dn) { 
           if (set_user_pwd($ldapconn, $dn)) { 
               echo "<p>Successfully set your password.</p>" ; 
           } else { 
               echo "<p>Cannot find you! Register to make an account.</p>"
           }
       }
    }
}// $ldapconn
} // POST

?>

<p>Please close your browser window now.</p>
</body>

