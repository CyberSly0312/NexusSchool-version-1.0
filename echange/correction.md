cd /var/www/html/NexusSchool-version-1.0-main

grep -RInE "TextInput::make\(['\"]password|->revealable\(|class .*Login" \
  app/Providers app/Filament 2>/dev/null
