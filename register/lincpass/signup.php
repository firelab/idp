<!DOCTYPE html>
<head>
<title>Signing Up <?php echo $_SERVER["SSL_CLIENT_S_DN_CN"]?></title>
</head>
<body>

<?php

//
// function enables or disables user in active directory.
//
function userenable($ds,$cn,$enable=1)
{
    // get user
    $sr = ldap_search($ds,"OU=Realms,DC=firelab,DC=org", 
                      "(cn=".$cn.")") ; 
    if ($sr) { 
        $fe = ldap_first_entry($ds, $sr) ; 
        if (!$fe) { 
            echo "Error fetching user entry." ; 
            return FALSE ; 
        }
        $attrs = ldap_get_attributes($ds,$fe) ; 
        if (!$attrs) { 
            echo "Error fetching user attributes." ; 
            return FALSE ; 
        }

        $dn = $attrs["distinguishedName"][0] ; 
        $ac = $attrs["userAccountControl"][0] ; 

        // Figure out the desired state.
        $disable_code=($ac |  2); // set all bits plus bit 1 (=dec2)
        $enable_code =($ac & ~2); // set all bits minus bit 1 (=dec2)
        if ($enable==1) $new=$enable_code; else $new=$disable_code; //enable or disable?

        // change account info if necessary (not in desired state)
        if ($new != $ac) { 
            $userdata=array();
            $userdata["useraccountcontrol"][0]=$new;
            //change state
            if (!ldap_modify($ds, $dn, $userdata)) {
                echo "Error enabling user!" ; 
            }
            $ac = $new ;
        }
        if (($ac & 2)==2) $status=0; else $status=1;
        return $status; //return current status (1=enabled, 0=disabled)
    } 
    return FALSE ; 
}

//
// Function creates a user in active directory based on the POSTed
// parameters and elements that Apache parsed out of the client 
// certificate.
//
function create_disabled_user($ds) 
{
    $upn_cert = $_SERVER["SSL_CLIENT_S_DN_UID"]."@fedidcard.gov";
    
    $upn_p    = $_POST["userPrincipalName"];
    $cn       = $_POST["cn"];
    $sn       = $_POST["sn"];
    $givenName= $_POST["givenName"];
    $dispName = $_POST["displayName"];
    $mail     = $_POST["mail"];
    $tel      = $_POST["telephoneNumber"];

    $dn = NULL ;

    // sanity check: make sure cert and provided data match
    if (strcasecmp($upn_cert, $upn_p) == 0) { 
        $info["userPrincipalName"] = $upn_cert; 
        $info["sAMAccountName"] = $cn;
        $info["sn"] = $sn; 
        $info["givenName"] = $givenName; 
        $info["displayName"] = $dispName; 
        $info["mail"] = $mail; 
        $info["telephoneNumber"] = $tel; 
        $info["objectClass"][0] = "top"; 
        $info["objectClass"][1] = "person" ; 
        $info["objectClass"][2] = "organizationalPerson" ; 
        $info["objectClass"][3] = "user" ; 

        $dn = "cn=".$cn.", ou=LincPass, ou=Realms, dc=firelab, dc=org" ; 

        if (!ldap_add($ds, $dn, $info)) { 
            // reset the dn variable if add failed.
            $dn = NULL  ; 
        }

    }

    return $dn ; 
}


//
// Function sets the password of the specified user object to the value 
// supplied in the POST parameters.
//
function set_user_pwd($ds,$dn) 
{
   $pwd = "\"".$_POST["password"]."\"" ; 
   echo "<p>Password is: ". $pwd . "</p>" ;
   $info["unicodePwd"] = iconv("UTF-8", "UTF-16LE", $pwd) ;

   if (!ldap_mod_replace( $ds, $dn, $info )) {
        echo "Failed to set password!"  ;
   }
}

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
        // set password
        set_user_pwd($ldapconn,$dn) ; 
        // enable user
        userenable($ldapconn,$_POST["cn"]) ; 
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
