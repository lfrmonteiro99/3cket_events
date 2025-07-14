# 3cket Events – Read-Only Event Management API

A robust, production-ready event management system built with PHP 8.2+, following Domain-Driven Design (DDD) and clean architecture. The API provides fast, reliable, and scalable read-only access to Portuguese event data, with advanced features for caching, logging, validation, and observability.

---

## Requirements

**Docker (Recommended):**
- Docker 20.10+
- Docker Compose 2.0+
- Git

**Manual/Local Development:**
- PHP 8.2+
- Composer 2.0+
- MySQL/MariaDB 8.0+ (optional, for DB mode)
- Web server (Nginx, Apache, or PHP built-in)
- Git
- PHP extensions: `pdo`, `json`, `mysql`/`mysqli` (required); `redis`, `opcache` (optional)
- Dev tools: PHPUnit 10+, PHPStan 2.1+, PHP-CS-Fixer 3.82+

---

## Installation

### Docker (Quick Start)
```bash
git clone https://github.com/lfrmonteiro99/3cket_events.git
cd 3cket_events
cp env.sample .env
docker buildx bake --load
docker-compose up -d
docker-compose exec app composer install
```
- Access: [http://localhost:8000/events](http://localhost:8000/events)

### Local Development
```bash
git clone https://github.com/lfrmonteiro99/3cket_events.git
cd 3cket_events
cp env.sample .env
composer install
# (Optional) Set up MySQL and update .env
php -S localhost:8000 -t public/
```
- Access: [http://localhost:8000/events](http://localhost:8000/events)

### Production
- Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
- Use `composer install --no-dev --optimize-autoloader`
- Point your web server to the `public/` directory
- Ensure `logs/` is writable

---

## Usage

### API Endpoints
- `GET /events` — List events (pagination, sorting)
- `GET /events/{id}` — Get event by ID
- `GET /search` — Search events (text, location, date, geo)
- `GET /search/nearby` — Find events near coordinates
- `GET /search/suggestions` — Get search suggestions
- `GET /debug` — System info
- `GET /cache` — Cache stats
- `GET /cache/analytics` — Cache analytics
- `POST /cache/warm-up` — Warm up cache
- `POST /cache/invalidate` — Invalidate all cache
- `POST /cache/invalidate/event/{id}` — Invalidate cache for event
- `POST /cache/invalidate/search` — Invalidate search cache

### Example Requests
```bash
curl http://localhost:8000/events
curl "http://localhost:8000/events?page=2&page_size=5&sort_by=event_name&sort_direction=DESC"
curl http://localhost:8000/events/1
curl http://localhost:8000/search?search=festival&location=Lisboa
curl http://localhost:8000/search/nearby?lat=38.7&lng=-9.1&radius=10
curl http://localhost:8000/debug
```

### Response Formats
- JSON (default)
- XML: `Accept: application/xml`
- CSV: `Accept: text/csv`
- HTML: `Accept: text/html`

### Development & Quality
```bash
composer test           # Run all tests
composer analyse        # Static analysis (PHPStan)
composer cs-fix         # Auto-fix code style
composer quality        # Run all quality checks
```

---

## Explanations

### Architecture
- **Domain-Driven Design:** Clear separation of domain, application, infrastructure, and presentation layers.
- **CQRS:** Query and command responsibilities are separated for clarity and scalability.
- **Repository Pattern:** Data access is abstracted for flexibility (DB, CSV, cache).
- **Service Providers:** Dependency injection and configuration are managed centrally.

### Features
- **Read-Only API:** No create, update, or delete operations.
- **Advanced Caching:** Multi-level (in-memory, Redis) with analytics and warm-up/invalidate endpoints.
- **Specialized Logging:** Monolog-based, with separate logs for errors, performance, requests, and application events.
- **Flexible Data Source:** Auto-fallback between database and CSV for resilience.
- **Comprehensive Validation:** All input parameters are validated, with clear error responses.
- **Extensive Testing:** Full suite of unit and integration tests for all endpoints and edge cases.
- **Multiple Response Formats:** JSON, XML, CSV, HTML supported via content negotiation.

### Configuration
- All settings are managed via `.env` (see `env.sample`).
- Key options: data source strategy, cache backend, logging, database, Redis, pagination, etc.

---

## Improvements & How to Do It

- **Add New Endpoints:**
  - Create a new controller in `src/Presentation/Controller/`
  - Register the route in `src/Router/RouteProvider.php`
  - Add use case/service logic in `src/Application/UseCase/` or `src/Application/Service/`
- **Support More Data Sources:**
  - Implement a new repository in `src/Infrastructure/Repository/`
  - Register it in the service provider
- **Enhance Caching:**
  - Add new cache strategies in `src/Infrastructure/Cache/`
  - Update cache analytics or warm-up logic as needed
- **Improve Validation:**
  - Add new validators in `src/Infrastructure/Validation/`
  - Register them in `ValidatorBag.php`
- **Expand Testing:**
  - Add tests in `tests/Unit/` or `tests/Integration/`
  - Use PHPUnit for all new code
- **Add Observability:**
  - Extend logging or add metrics in `src/Infrastructure/Logging/`

---

## Tips for Deploy

- Use Docker for consistent, reproducible deployments
- Set `APP_ENV=production` and `APP_DEBUG=false` in production
- Use `composer install --no-dev --optimize-autoloader` for optimized autoloading
- Point your web server (Nginx/Apache) to the `public/` directory
- Ensure `logs/` is writable by the web server user
- Configure Redis for distributed caching in multi-server setups
- Use rotating log files for long-term log management
- Monitor logs and cache analytics for health and performance

---

## Tips for Scalability

- Use Redis as the cache backend for distributed, multi-instance deployments
- Deploy behind a load balancer for horizontal scaling
- Use persistent database connections and connection pooling
- Enable PHP-FPM process management for concurrency
- Use Docker Compose or Kubernetes for orchestration
- Monitor cache hit/miss rates and tune `CACHE_TTL` for optimal performance
- Separate read replicas for the database if needed
- Use log aggregation and monitoring tools for observability

---

**Built with ❤️ in Portugal** 🇵🇹
