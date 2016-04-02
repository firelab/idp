# PKI Certificates for LincPass PIV

All certificates are base-64 encoded PEM files. These are suitable for 
deployment on an Apache webserver, tomcat servelet, MIT kerberos KDC, Active
Directory server, or Windows workstation.

The *lincpass_ca* directory contains individual certificates in the chain as 
separate files. The *lincpass_ca.crt* file is the concatenation of all three 
certificates in one file. Use what is most appropriate for your application.
