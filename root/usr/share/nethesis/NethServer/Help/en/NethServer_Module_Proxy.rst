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
    Each user will be forced to enter user name and password.
    Squid will listen on port 3128. All client must be explicitly configured to use the proxy.

Transparent
    All HTTP traffic on port 80 will be redirect through the proxy.
    No client configuration is needed.

Transparent with SSL
    All HTTP and HTTPS traffic on port 80 and 443 will be redirect through the proxy.
    No client configuration is needed.

Block HTTP and HTTPS ports
    If enabled, clients will not be able to bypass the proxy.
    External sites on ports 80 and 443 will be reachable only using the proxy.

Parent proxy
    Enter the IP address and port of the parent proxy. The correct syntax is
    IP_Address:port.
    *DO NOT* enter the IP address of this server.

Domains without proxy
=====================

List of domains bypassed when transparent proxy is enabled.

Requirement: all clients must use this machine as DNS server.

Domains
    List of domains, one per line.
    Each entry matches the domain itself and all sub-domains.

Hosts without proxy
===================

List of internal hosts allowed to bypass the transparent proxy and access
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

List of remote hosts that need to be accessed directly.
It's useful for badly written sites which don't work through a proxy.

Name
    A unique name for the bypass rule.

Enabled
    Enable or disable the bypass rule.

Host
    Select a host between remote hosts and firewall objects.

Description
    Custom description (optional).

Rules
=====

Change the priority or the output WAN for traffic which goes through the proxy.

Rule #
    Auto-generated identification number

Enable rule
    If checked, the rule is enabled.

Source
    Select a host, IP range or CIDR between firewall objects.

Action
    There 3 actions available:

    - *Force to <provide>*: force the traffic from :guilabel:`Source` to the selected
      provider. If the provider is down, the remote hosts will not be reachable
      from the selected source

    - *Route to <provider>*: divert the traffic from :guilabel:`Source` to the selected provider.
      If the provider is down, the traffic will be routed accordingly to the
      configured multi wan policy.

    - *Low priority*: lower the priority of the traffic from :guilabel:`Source`

    - *High priority*: raise the priority of the traffic from :guilabel:`Source`
     

Destination domains
    Accept a list of domain names.
    If left blank, the rule applies to any contacted remote host,
    otherwise the rule applies only if involved domain is listed.

Description
    Custom description (optional).

Cache
=====
Cache configuration of squid (enabled o disabled) by setting these parameters:

Disk cache size
    Set (in MB) the size of cache

Min object size
    Set (in kB) the minimum size of cache object.

Max object size
    Set (in kB) the maximum size of cache object.
