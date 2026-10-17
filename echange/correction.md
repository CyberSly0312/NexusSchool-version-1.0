echo "=== IP TAILSCALE ==="
tailscale ip -4

echo "=== DNSMASQ ==="
sudo systemctl status dnsmasq --no-pager

echo "=== REGLE NEXUSSCHOOL ==="
sudo grep -R "nexusschool.test" /etc/dnsmasq.conf /etc/dnsmasq.d/ 2>/dev/null

echo "=== PORT DNS 53 ==="
sudo ss -lntup | grep ':53 '


vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== IP TAILSCALE ===" tailscale ip -4
=== IP TAILSCALE === tailscale ip -4
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== DNSMASQ ===" sudo systemctl status dnsmasq --no-pager
=== DNSMASQ === sudo systemctl status dnsmasq --no-pager
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== REGLE NEXUSSCHOOL ===" sudo grep -R "nexusschool.test" /etc/dnsmasq.conf /etc/dnsmasq.d/ 2>/dev/null
=== REGLE NEXUSSCHOOL === sudo grep -R nexusschool.test /etc/dnsmasq.conf /etc/dnsmasq.d/
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== PORT DNS 53 ===" sudo ss -lntup | grep ':53 '
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ 
