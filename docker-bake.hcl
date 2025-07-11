# Docker Buildx Bake configuration for 3cket Event Management System
# This enables faster, parallel builds with advanced caching

variable "TAG" {
  default = "latest"
}

variable "REGISTRY" {
  default = "3cket"
}

# Build targets
target "app" {
  context = "."
  dockerfile = "Dockerfile"
  tags = ["${REGISTRY}/php-app:${TAG}"]
  platforms = ["linux/amd64", "linux/arm64"]
  
  # Enable BuildKit features
  target = "production"
  
  # Cache configuration for faster builds
  cache-from = [
    "type=local,src=.buildx-cache"
  ]
  cache-to = [
    "type=local,dest=.buildx-cache,mode=max"
  ]
  
  # Build arguments
  args = {
    PHP_VERSION = "8.2"
    COMPOSER_VERSION = "latest"
  }
}

# Development target (faster for local development)
target "app-dev" {
  inherits = ["app"]
  target = "development"
  tags = ["${REGISTRY}/php-app:dev"]
  
  # Skip multi-platform for dev builds (faster)
  platforms = ["linux/amd64"]
}

# Group for building all images
group "default" {
  targets = ["app"]
}

group "dev" {
  targets = ["app-dev"]
} 