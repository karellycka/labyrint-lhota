#!/bin/bash
set -e

# Disable all MPM modules
a2dismod mpm_event mpm_worker 2>/dev/null || true

# Enable only mpm_prefork (required for PHP)
a2enmod mpm_prefork 2>/dev/null || true

# Execute the default Apache foreground command
exec apache2-foreground
