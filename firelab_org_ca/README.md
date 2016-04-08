# PKI Certificates for Firelab External Active Directory

All certificates are base-64 encoded PEM files. These are suitable for 
deployment on an Apache webserver, tomcat servelet, MIT kerberos KDC, Active
Directory server, or Windows workstation.

The *firelab_org_ca* directory contains individual certificates in the chain as 
separate files. The *ad_firelab_org_bundle.crt* file is the concatenation of 
both certificates in one file. Use what is most appropriate for your 
application.
