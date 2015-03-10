Summary: NethServer squid configuration
Name: nethserver-squid
Version: 1.3.1
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name} 
Source0: %{name}-%{version}.tar.gz
BuildArch: noarch

Requires: nethserver-firewall-base, nethserver-httpd
Requires: squid >= 3.3.10

BuildRequires: perl
BuildRequires: nethserver-devtools 

%description
NethServer squid configuration

%prep
%setup

%build
%{makedocs}
perl createlinks

%install
rm -rf $RPM_BUILD_ROOT
(cd root; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)
%{genfilelist} $RPM_BUILD_ROOT --file /usr/libexec/nethserver/squid_pam_helper "%attr(4755,root,root)" > %{name}-%{version}-filelist
echo "%doc COPYING" >> %{name}-%{version}-filelist

%post

%preun

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)

%changelog
* Tue Mar 10 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.1-1
- Web proxy: Active Directory authentication not working  - Bug #2984 [NethServer]
- squid cache management - Feature #2981 [NethServer]

* Tue Jan 20 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.0-1.ns6
- Transparent proxy: switch iplementation from TPROXY to REDIRECT - Enhancement #2967 [NethServer]
- Proxy: intercept SSL connections (SSL transparent) - Enhancement #2977 [NethServer]
- Adjust squid.conf for better performances - Enhancement #2973 [NethServer]
- Web proxy: support blue zones - Enhancement #2964 [NethServer]
- Add port 980 to safe/ssl ports on squid configuration - Enhancement #2905 [NethServer]
- Web proxy: bypass rules based on destination and source - Feature #2503 [NethServer]
- Enhance upstream proxy validator - Enhancement #2736 [NethServer]

* Wed Aug 20 2014 Davide Principi <davide.principi@nethesis.it> - 1.2.0-1.ns6
- Proxy: block ports 80 (http) and 443 (https) - Feature #2777 [NethServer]
- Web proxy: refactor squid.conf template - Enhancement #2704 [NethServer]

* Wed Feb 05 2014 Davide Principi <davide.principi@nethesis.it> - 1.1.1-1.ns6
- NethCamp 2014 - Task #2618 [NethServer]
- Kerberos keytab file is missing for new services - Bug #2407 [NethServer]
- Squid GSSAPI/GSS-Negotiate (Kerberos) authentication - Feature #1987 [NethServer]
- Update all inline help documentation - Task #1780 [NethServer]

* Wed Dec 18 2013 Davide Principi <davide.principi@nethesis.it> - 1.1.0-1.ns6
- Kerberos keytab file is missing for new services - Bug #2407 [NethServer]
- Squid GSSAPI/GSS-Negotiate (Kerberos) authentication - Feature #1987 [NethServer]

* Wed Oct 16 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.4-1.ns6
- Add proxy bypass rules #2072
- Allow web traffic when Squid mode is transparent and disabled #2111
- Db defaults: remove Runlevels prop #2067

* Fri Jul 26 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.3-1.ns6
- Change /etc/squid.conf permissions #1959
- Change wpad template fragment order in /etc/hosts #17

* Fri Jul 19 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.2-1.ns6
- Restart squid on nethserver-squid-save and nethserver-squid-update events #1733
- Use perl setuid wrapper for pam authentitcation #1773
- Add bypass rules for trasparent proxy (without web ui) #2072

* Tue Jul 16 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.1-1.ns6
- Add missing translations #1773

* Thu Jun 20 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1.ns6
- First release with wpad support  #1773 #17

