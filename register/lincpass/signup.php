<!DOCTYPE html>
<?php
require_once "userfuncs.php" ; 
?>


<head>
<title>Signing Up <?php echo $_SERVER["SSL_CLIENT_S_DN_CN"]?></title>
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
        // create user (disabled/no password)
        $dn = create_disabled_user($ldapconn);
        if ($dn) { 
            // set password
            set_user_pwd($ldapconn,$dn) ; 
            // enable user
            userenable($ldapconn,$_POST["cn"]) ; 
            // add user to Insiders group
            add_user_to_group($ldapconn,
                      "cn=Insiders,cn=Users,dc=firelab,dc=org", $dn) ;

            echo "<ol><li>Registered account for ".$_POST["cn"] ;
            echo " (".$_SERVER["SSL_CLIENT_S_DN_CN"].")</li>" ;
            echo "<li>You never need to do this again.</li>";
            echo "<li>You may now close your browser window.</li></ol>" ; 
        }
    } else {
        // set http error status and maybe return something helpful
    }


    ldap_close($ldapconn);

}
} else { 
    http_response_code(405) ; 
}
?>

</body>
