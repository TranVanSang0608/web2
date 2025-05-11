#!/bin/bash

# Extract CSS from Sass files for production

echo "Extracting CSS from Sass files..."

# Create output directory
mkdir -p public/build/assets

# Copy Bootstrap CSS directly
if [ -f "node_modules/bootstrap/dist/css/bootstrap.min.css" ]; then
  echo "Copying Bootstrap CSS..."
  cp node_modules/bootstrap/dist/css/bootstrap.min.css public/build/assets/bootstrap.css
fi

# Try to compile Sass with node-sass if available
if command -v node-sass &> /dev/null; then
  echo "Compiling Sass with node-sass..."
  node-sass resources/sass/app.scss public/build/assets/app.css --output-style compressed
else
  # Fallback to simple file copy
  echo "node-sass not available, copying Sass file as CSS..."
  cp resources/sass/app.scss public/build/assets/app.css
fi

# Copy any existing CSS files from resources
if [ -d "resources/css" ]; then
  echo "Copying CSS files from resources/css..."
  cp -r resources/css/* public/build/assets/ 2>/dev/null || true
fi

# Copy our fallback CSS as a safety measure
if [ -f "public/css/app-fallback.css" ]; then
  echo "Copying fallback CSS..."
  cp public/css/app-fallback.css public/build/assets/app-fallback.css
fi

# Set correct permissions
chmod -R 755 public/build

echo "CSS extraction complete!"
