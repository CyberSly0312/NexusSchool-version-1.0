echo "=== IP TAILSCALE ==="
tailscale ip -4

echo "=== DNSMASQ ==="
sudo systemctl status dnsmasq --no-pager

echo "=== REGLE NEXUSSCHOOL ==="
sudo grep -R "nexusschool.test" /etc/dnsmasq.conf /etc/dnsmasq.d/ 2>/dev/null

echo "=== PORT DNS 53 ==="
sudo ss -lntup | grep ':53 '
