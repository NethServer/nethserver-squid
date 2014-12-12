=========
Web proxy 
=========

Configure the web proxy (Squid) and bypass rules.

Proxy
=====

The web proxy works to reduce bandwidth usage by caching
the pages you visit. It can also enforce content filtering.

Web proxy can be enabled only on green (local networks) and blue (guest networks) zones.

Manual
    Squid will listen on port 3128. All client must be explicitly configured to use the proxy.

Authenticated
    Each user will be forced to enter username and password.
    Squid will listen on port 3128. All client must be explicitly configured to use the proxy.

Transparent
    All HTTP traffic will be redirect through the proxy.
    No client configuration is needed.

Transparent with SSL
    All HTTP and HTTPS traffic will be redirect through the proxy.
    The server certificate (CA) must be installed on each client to allow HTTPS traffic.

Block HTTP and HTTPS ports
    If enabled, clients will not be able to bypass the proxy.
    Ports 80 and 443 will be reachable only using the proxy.

Parent proxy
    Enter the IP address and port of the parent proxy. The correct syntax is
    IP_Address:port.
    *DO NOT* enter the IP address of this server.

Hosts without proxy
===================

Set up some hosts allowed to bypass the transparent proxy and access
Internet without being filtered.

Name
    A unique name for the bypass rule.

Enabled
    Enable or disable the bypass rule.

Host
    Select a host between local machines and firewall objects.

Description
    Custom description (optional).

Sites without proxy
===================

Setup some remote hosts that need to be accessed directly.
It's useful for bad-written sites which doesn't correctly work with proxy.

Name
    A unique name for the bypass rule.

Enabled
    Enable or disable the bypass rule.

Host
    Select a host between remote hosts and firewall objects.

Description
    Custom description (optional).

