#!/bin/bash

# This script fixes permissions in Laravel storage directories
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set permissions
chmod -R 777 storage
chmod -R 777 bootstrap/cache

echo "Permissions fixed!"
