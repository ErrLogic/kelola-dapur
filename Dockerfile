# ========================
# Builder stage
# ========================
FROM dunglas/frankenphp:1-php8.3-bookworm AS builder

WORKDIR /app

# Update dan install build toolchain + dev libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
      build-essential autoconf pkg-config \
      libzip-dev zlib1g-dev \
      libavif-dev libwebp-dev libxpm-dev \
      libpng-dev libjpeg-dev libfreetype6-dev \
      libpq-dev libsodium-dev libffi-dev \
      libssl-dev git unzip curl \
 && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN install-php-extensions \
      pdo_pgsql pgsql gd bcmath sockets exif pcntl ffi redis igbinary zip calendar

# Copy composer files first (better caching)
COPY composer.json composer.lock ./

# Composer binary (dari official image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install prod dependencies only
RUN composer install --prefer-dist --no-scripts --no-progress --no-interaction --optimize-autoloader \
 && composer clear-cache && rm -rf /root/.composer /tmp/*

# Keep compiled extensions + confs for runtime stage
RUN mkdir -p /exts/extensions /exts/conf.d \
 && cp -a "$(php -r 'echo ini_get("extension_dir");')"/*.so /exts/extensions/ \
 && cp -a /usr/local/etc/php/conf.d/* /exts/conf.d/

# Copy full source (after composer for better layer caching)
COPY . .

# ========================
# Runtime stage
# ========================
FROM dunglas/frankenphp:1-php8.3-bookworm AS runtime

WORKDIR /app

# Install only runtime libraries (no compilers, no -dev)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq5 libsodium23 libffi8 libzip4 \
    libpng16-16 libjpeg62-turbo libfreetype6 \
    libavif15 libwebp7 libxpm4 liblz4-1 \
    supervisor \
 && rm -rf /var/lib/apt/lists/*

# Install Node.js v24.13.1
RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
 && apt-get install -y nodejs \
 && node --version \
 && npm --version

# Copy compiled PHP extensions and configs from builder
COPY --from=builder /exts/extensions/ /usr/local/lib/php/extensions/no-debug-zts-20230831/
COPY --from=builder /exts/conf.d/ /usr/local/etc/php/conf.d/

# Copy vendor and application source
COPY --from=builder /app ./

# Container configuration
COPY supervisord.conf /etc/supervisor/conf.d/
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8090

# Use startup by default
CMD ["/usr/local/bin/start.sh"]
