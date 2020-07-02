Summary: NethServer squid configuration
Name: nethserver-squid
Version: 1.10.10
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name}
Source0: %{name}-%{version}.tar.gz
Source1: %{name}.tar.gz
BuildArch: noarch

Requires: nethserver-firewall-base, nethserver-httpd
Requires: squid >= 3.5.20
Requires: nethserver-sssd

BuildRequires: perl
BuildRequires: nethserver-devtools

%description
NethServer squid configuration

%prep
%setup

%build
%{makedocs}
perl createlinks
sed -i 's/_RELEASE_/%{version}/' %{name}.json

%install
rm -rf %{buildroot}
(cd root; find . -depth -print | cpio -dump %{buildroot})

mkdir -p %{buildroot}/usr/share/cockpit/%{name}/
mkdir -p %{buildroot}/usr/share/cockpit/nethserver/applications/
mkdir -p %{buildroot}/usr/libexec/nethserver/api/%{name}/
tar xvf %{SOURCE1} -C %{buildroot}/usr/share/cockpit/%{name}/
cp -a %{name}.json %{buildroot}/usr/share/cockpit/nethserver/applications/
cp -a api/* %{buildroot}/usr/libexec/nethserver/api/%{name}/

%{genfilelist} %{buildroot} --file /etc/sudoers.d/50_nsapi_nethserver_squid 'attr(0440,root,root)' > %{name}-%{version}-filelist
echo "%doc COPYING" >> %{name}-%{version}-filelist

%post

%preun

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)
%dir %{_nseventsdir}/%{name}-update

%changelog
* Thu Jul 02 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.10.10-1
- Human readable numbers in Cockpit dashboards - NethServer/dev#6206

* Wed Jun 03 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.10.9-1
- Cockpit: Web proxy status unavailable - Bug NethServer/dev#6185

* Tue Apr 07 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.10.8-1
- Restore configuration without network override - NethServer/dev#6099

* Mon Mar 02 2020 Davide Principi <davide.principi@nethesis.it> - 1.10.7-1
- Failed validation for proxy destination bypass - Bug NethServer/dev#6069

* Wed Feb 05 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.10.6-1
- Cockpit - Web Filter. Add "Select all" button for content filter categories - Nethserver/dev#6047

* Mon Dec 02 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.10.5-1
- Cockpit: DNS host proxy bypass ignored - Bug NethServer/dev#5971

* Thu Nov 21 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.10.4-1
- Email: domain menu not correctly shown - Bug Nethserver/dev#5942

* Tue Nov 19 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.10.3-1
- Cockpit: web content filter categories list not updated - Bug NethServer/dev#5917

* Fri Nov 15 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.10.2-1
- cockpit: web proxy src bypass ignored - Bug NethServer/dev#5892

* Mon Oct 28 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.10.1-1
- Logs page in Cockpit - Bug NethServer/dev#5866
- Cockpit: missing validators on proxy web panel - Bug Nethserver/dev#5885

* Tue Oct 15 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.10.0-1
- Squid: corrupted access log - Bug NethServer/dev#5865

* Thu Oct 10 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.9.1-1
- Cockpit: improve English labels - NethServer/dev#5856

* Tue Oct 01 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.9.0-1
- Sudoers based authorizations for Cockpit UI - NethServer/dev#5805

* Wed Sep 11 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.8.4-1
- Improve charts unavailable label (#39) - Thanks to dnutan

* Tue Sep 03 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.8.3-1
- Cockpit. List correct application version - NethServer/dev#5819

* Fri Jun 14 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.8.2-1
- dnsmasq fails with many web proxy domain bypass - Bug NethServer/dev#5775

* Tue Jun 11 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.8.1-1
- api: correctly handle clamd@squidclamav
- ui. set last visited route

* Tue May 28 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.8.0-1
- Web proxy Cockpit UI - NethServer/dev#5746

* Fri Jan 18 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.7.4-1
- nethserver-squid : failure of template expansion on armhfp - Bug NethServer/dev#5680

* Tue Nov 27 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.7.3-1
- Web proxy rules don't show shaping priorities - Bug NethServer/dev#5656

* Fri Oct 12 2018 Stephane de Labrusse <stephdl@de-labrusse.fr> - 1.7.2-1
- Package nethserver-X must subscribe nethserver-sssd-save - NethServer/dev#5600

* Mon Jul 02 2018 Davide Principi <davide.principi@nethesis.it> - 1.7.1-1
- Fix of keytab backup configuration expansion - NethServer/dev#5536

* Tue Jul 18 2017 Davide Principi <davide.principi@nethesis.it> - 1.7.0-1
- Web proxy: support divert and priority rules - NethServer/dev#5327

* Thu Jun 01 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.6.0-1
- Web proxy: support bypass by domains - NethServer/dev#5299

* Thu Apr 20 2017 Davide Principi <davide.principi@nethesis.it> - 1.5.4-1
- Squid: integrated authentication doesn't work - Bug NethServer/dev#5259
- squid: trusted networks not enabled - Bug NethServer/dev#5261

* Wed Feb 15 2017 Davide Principi <davide.principi@nethesis.it> - 1.5.3-1
- Squid parent proxy can no be set to ADDRESS:PORT in UI - Bug NethServer/dev#5217

* Mon Jan 16 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.2-1
- Web-proxy is interfering with the samba fileserver - Bug NethServer/dev#5194

* Tue Jan 03 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.1-1
- Disable squid ssl certificate generation - NethServer/dev#5176

* Thu Dec 15 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.0-1
- Transparent HTTPS proxy - NethServer/dev#5169

* Thu Jul 07 2016 Stefano Fancello <stefano.fancello@nethesis.it> - 1.4.0-1
- First NS7 release

* Tue Dec 01 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.11-1
- Add CIDR subnets and ip ranges as hosts without proxy in Proxy - Enhancement #3323 [NethServer]
- WPAD improvements - Enhancement #3266 [NethServer]
- Entire Subnet and Ip Ranges Exclusion in Proxy - Enhancement #3226 [NethServer]

* Tue Nov 10 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.10-1
- Configure forward_max_tries for Squid - Bug #3288 [NethServer]
- squid log file rotation - Enhancement #3290 [NethServer]

* Mon Oct 12 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.9-1
- New zones can't browse the net thorugh proxy - Bug #3275 [NethServer]
- Web Proxy http port block - Bug #3268 [NethServer]

* Tue Sep 29 2015 Davide Principi <davide.principi@nethesis.it> - 1.3.8-1
- Make Italian language pack optional - Enhancement #3265 [NethServer]

* Thu Aug 27 2015 Davide Principi <davide.principi@nethesis.it> - 1.3.7-1
- Web proxy: rules error if port blocking enabled with pppoe - Bug #3240 [NethServer]

* Mon Jun 22 2015 Davide Principi <davide.principi@nethesis.it> - 1.3.6-1
- SquidGuard profiles not working when proxy authenticated with AD  - Enhancement #3178 [NethServer]
- Fixed nethserver-squid-conf on keytab re-initialization. Was Bug #2407 [NethServer]

* Tue May 19 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.5-1
- web proxy does not cache objects bigger than 4Mb - Bug #3144 [NethServer]

* Tue Apr 21 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.4-1
- Proxy http doesn't allow http traffic when set in SSL transparent mode - Bug #3128 [NethServer]

* Thu Apr 09 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.3-1
- Web proxy: add prop for squid SSL_ports - Enhancement #3106 [NethServer]
- squid stop after restore if cache enabled - Bug #3105 [NethServer]
- Web proxy: add property for Squid safe ports - Enhancement #3101 [NethServer]
- Web proxy: exclude local sites when mode is transparent - Enhancement #3099 [NethServer]

* Thu Mar 12 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.2-1
- Can't access Squid from blue network when proxy is configured in manual or authenticated mode - Bug #3086 [NethServer]
- HTTP and HTTPS port blocked even if proxy is disabled - Bug #3050 [NethServer]
- Allow authentication if samba is configured in WS mode  - Bug #2984 [NethServer]

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

