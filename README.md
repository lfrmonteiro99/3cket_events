# 3cket Event Management System

A sophisticated event management system built with modern PHP 8.2+ following Domain-Driven Design (DDD) principles. The system provides comprehensive event management capabilities with enterprise-grade features including intelligent caching, pagination, connection pooling, specialized logging with **Monolog**, and extensive API functionality.

## Requirements

### For Docker Usage (Recommended - Easy Setup)
- **Docker**: 20.10+ 
- **Docker Compose**: 2.0+
- **Git**: For downloading the code

> **That's it!** Docker automatically installs PHP, database, and all required extensions for you.

### For Local Development (Manual Setup)
- **PHP**: 8.2 or higher
- **Composer**: 2.0 or higher
- **MySQL/MariaDB**: 8.0+ database server
- **Web Server**: Nginx, Apache, or use PHP's built-in server
- **Git**: For downloading the code

#### Required PHP Extensions (Local Development Only)
- `ext-pdo` - Database connectivity
- `ext-json` - JSON handling
- `ext-mysql` or `ext-mysqli` - MySQL database support

#### Optional PHP Extensions (Local Development Only)
- `ext-redis` - Redis cache support (for better performance)
- `ext-opcache` - PHP opcode caching (recommended for production)

#### Development Tools (Local Development Only)
- **PHPUnit**: 10.0+ (for running tests)
- **PHPStan**: 2.1+ (for code analysis)
- **PHP-CS-Fixer**: 3.82+ (for code formatting)

> **Note**: If using Docker, all PHP extensions and development tools are automatically installed.

## Installation

Choose the installation method that works best for you:

### Option 1: Docker Setup (Recommended - Super Easy!)

> **Best for**: Beginners, quick testing, or if you don't want to install PHP manually

1. **Download the code**
   ```bash
   git clone https://github.com/lfrmonteiro99/3cket_events.git
   cd 3cket_events
   ```

2. **Copy environment configuration**
   ```bash
   cp env.sample .env
   ```

3. **Build the application containers (using Docker Buildx for 99% faster builds)**
   ```bash
   docker buildx bake --load
   ```

4. **Start the application services**
   ```bash
   docker-compose up -d
   ```

5. **Install dependencies**
   ```bash
   docker-compose exec app composer install
   ```

6. **That's it!** 
   - Open your browser and go to: `http://localhost:8000/events`
   - The application is now running with everything configured automatically

### Option 2: Local Development (Manual Setup)

1. **Download the code**
   ```bash
   git clone https://github.com/lfrmonteiro99/3cket_events.git
   cd 3cket_events
   ```

2. **Copy configuration file**
   ```bash
   cp env.sample .env
   ```

3. **Install PHP dependencies**
   ```bash
   composer install
   ```

4. **Set up your database**
   ```bash
   # Create a MySQL database named '3cket_events'
   # Update the .env file with your database settings:
   # DB_HOST=localhost
   # DB_USERNAME=your_username
   # DB_PASSWORD=your_password
   ```

5. **Start the application**
   ```bash
   php -S localhost:8000 -t public/
   ```

6. **Test it works**
   - Open your browser and go to: `http://localhost:8000/events`

### Option 3: Production Deployment

> **Best for**: Deploying to a live server

1. **Download the code**
   ```bash
   git clone https://github.com/lfrmonteiro99/3cket_events.git
   cd 3cket_events
   ```

2. **Install production dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Configure for production**
   ```bash
   cp env.sample .env
   # Edit .env file and set:
   # APP_ENV=production
   # APP_DEBUG=false
   # Configure your database settings
   ```

4. **Set up web server**
   - Point your web server (Nginx/Apache) to the `public/` directory
   - Ensure the web server can write to the `logs/` directory

5. **Test your deployment**
   - Visit your domain and check: `https://your-domain.com/events`

---

## Verify Your Installation

After installation, test these URLs in your browser:

- **API Test**: `http://localhost:8000/events` - Should show 25 Portuguese events
- **System Info**: `http://localhost:8000/debug` - Should show system information
- **Health Check**: `http://localhost:8000/cache?action=stats` - Should show cache statistics

If you see JSON data with Portuguese events, congratulations! Your installation is working.

## Usage

### Basic API Usage

The system provides a RESTful API for event management featuring **25 authentic Portuguese events** across municipalities including Lisboa, Porto, Coimbra, Braga, Aveiro, and more:

#### List Events (with pagination)
```bash
# Get all events with default pagination
curl http://localhost:8000/events

# Get events with custom pagination
curl "http://localhost:8000/events?page=2&page_size=5&sort_by=event_name&sort_direction=DESC"
```

#### Get Specific Event
```bash
# Get event by ID
curl http://localhost:8000/events/1
```

#### Debug Information
```bash
# Get system debug information
curl http://localhost:8000/debug

# Get cache statistics
curl http://localhost:8000/cache?action=stats

# Clear cache
curl http://localhost:8000/cache?action=clear
```

### Response Formats

The API supports multiple response formats:

#### JSON (default)
```bash
curl -H "Accept: application/json" http://localhost:8000/events
```

#### XML
```bash
curl -H "Accept: application/xml" http://localhost:8000/events
```

#### CSV
```bash
curl -H "Accept: text/csv" http://localhost:8000/events
```

#### HTML
```bash
curl -H "Accept: text/html" http://localhost:8000/events
```

### Development Commands

#### Testing
```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test suite
vendor/bin/phpunit tests/Unit/Application/Service/EventServiceTest.php
```

#### Code Quality
```bash
# Run static analysis
composer analyse

# Fix code style
composer cs-fix

# Check code style
composer cs-check

# Run all quality checks
composer quality
```

#### Docker Development
```bash
# Build optimized containers
docker buildx bake --load

# Start development environment
docker-compose up -d

# View logs
docker-compose logs -f app

# Execute commands in container
docker-compose exec app composer install
```

## Portuguese Events Data

The application now contains **25 authentic Portuguese events** across **25 municipalities**:

### Featured Events & Locations
- **Festival de Fado** - Lisboa
- **Feira de Artesanato** - Porto
- **Concerto de Música Clássica** - Coimbra
- **Festival Gastronómico** - Braga
- **Mostra de Cinema** - Aveiro
- **Feira Medieval** - Óbidos
- **Festival de Verão** - Faro
- **Encontro de Folclore** - Viana do Castelo
- **Exposição de Arte** - Viseu
- **Festival de Jazz** - Leiria
- **Mercado de Natal** - Guimarães
- **Corrida Popular** - Setúbal
- **Festival de Teatro** - Évora
- **Feira de Livros** - Santarém
- **Concerto de Rock** - Beja
- **Mostra de Vinhos** - Vila Real
- **Festival de Dança** - Castelo Branco
- **Feira de Produtos Regionais** - Portalegre
- **Exposição de Fotografia** - Bragança
- **Festival de Música Popular** - Guarda
- **Concerto de Orquestra** - Funchal
- **Festival de Folclore** - Angra do Heroísmo
- **Mostra de Artesanato** - Torres Vedras
- **Festival de Verão** - Caldas da Rainha
- **Feira de Antiguidades** - Lamego

All events include accurate GPS coordinates and realistic Portuguese event names!

## Key Features

### Domain-Driven Design Architecture
- **Clean Architecture**: Separated concerns with clear boundaries
- **Rich Domain Model**: Entities with business logic
- **Value Objects**: Immutable objects with validation
- **Domain Services**: Business logic spanning multiple entities
- **CQRS Pattern**: Command Query Responsibility Segregation

### High Performance
- **Multi-level Caching**: Repository-level caching with multiple backends
- **Connection Pooling**: Persistent database connections
- **Intelligent Data Sources**: Auto-fallback between database and CSV
- **Optimized Queries**: Efficient pagination and sorting

### Advanced Pagination
- **Flexible Parameters**: Page size, sorting, direction
- **Cached Results**: Intelligent caching of paginated data
- **Metadata**: Complete pagination information
- **Performance**: 37x faster with caching

### Specialized Logging with Monolog
- **Separate Log Files**: Errors, performance, requests, application
- **Automatic Routing**: Smart log categorization
- **Structured Data**: JSON context for better analysis
- **Production Ready**: Configurable levels and formats
- **Enterprise Grade**: Built with Monolog 3.0

### Comprehensive Testing
- **151 Tests**: Complete test coverage
- **413 Assertions**: Thorough validation
- **Multiple Layers**: Unit, integration, and service tests
- **Quality Assurance**: PHPStan Level 8 compliance (0 errors)

## Architecture

### Domain-Driven Design Structure

```
src/
├── Domain/              # Core Business Logic
│   ├── Entity/          # Domain Entities (Event)
│   ├── ValueObject/     # Value Objects (Coordinates, EventName, Location, EventId)
│   ├── Service/         # Domain Services (EventDomainService)
│   ├── Event/           # Domain Events
│   └── Repository/      # Repository Interfaces
│
├── Application/         # Use Cases & Application Logic
│   ├── UseCase/         # Use Cases (GetAllEvents, GetEventById, GetPaginatedEvents)
│   ├── Service/         # Application Services (EventService)
│   ├── DTO/             # Data Transfer Objects (EventDto, PaginatedResponse)
│   ├── Query/           # Query Objects (CQRS)
│   ├── Command/         # Command Objects (CQRS)
│   └── Mapper/          # Entity ↔ DTO Mapping
│
├── Infrastructure/      # External Concerns
│   ├── Repository/      # Repository Implementations (Database, CSV, Cached)
│   ├── Cache/           # Caching (InMemory, Redis, Null, Strategy Enums)
│   ├── Database/        # Database Connections
│   ├── Logging/         # Specialized Monolog Logging System
│   ├── Response/        # Response Formatters
│   └── Validation/      # Input Validation
│
├── Presentation/        # User Interface
│   ├── Controller/      # HTTP Controllers
│   └── Response/        # Response Objects
│
└── Service/            # DI Container & Configuration
    └── Providers/       # Service Providers
```

## Configuration

### Environment Variables

#### Application Settings
```bash
APP_ENV=development          # Environment (development, production)
APP_DEBUG=true              # Debug mode
APP_NAME="3cket PHP Challenge"
```

#### Database Configuration
```bash
DB_HOST=db                  # Database host
DB_PORT=3306               # Database port
DB_DATABASE=3cket_events   # Database name
DB_USERNAME=3cket_user     # Database username
DB_PASSWORD=3cket_password # Database password
```

#### Cache Configuration
```bash
CACHE_STRATEGY=auto        # Cache strategy (auto, redis, memory, none)
CACHE_TTL=3600            # Cache TTL in seconds
CACHE_PREFIX=3cket:       # Cache key prefix
REDIS_HOST=redis          # Redis host
REDIS_PORT=6379           # Redis port
```

#### Logging Configuration
```bash
LOG_LEVEL=INFO            # Log level (DEBUG, INFO, WARNING, ERROR)
LOG_FORMAT=line           # Log format (line, json)
LOG_HANDLER=file          # Log handler (file, stdout, stderr, rotating)
```

#### Data Source Configuration
```bash
DATA_SOURCE_STRATEGY=auto # Data source strategy (auto, database, csv)
```

## Performance Features

### Multi-Level Caching Strategy

#### Cache Performance Results
| Operation | No Cache | With Cache | Improvement |
|-----------|----------|------------|-------------|
| **findAll()** | ~50ms | ~1ms | **50x faster** |
| **findById()** | ~10ms | ~0.5ms | **20x faster** |
| **Pagination** | ~30ms | ~0.8ms | **37x faster** |

#### Cache Backends
- **InMemoryCache**: Fast, single-process caching
- **RedisCache**: Distributed caching for multi-server setups
- **NullCache**: Disabled caching for testing
- **Auto Strategy**: Intelligent backend selection

### Connection Pooling
- **Container-Managed**: DI container singleton pattern
- **PDO Persistent**: Cross-request connection reuse
- **PHP-FPM Pooling**: Multiple processes for concurrency

## Logging System

### Specialized Log Files with Monolog

The application uses **Monolog 3.0** with specialized logging that routes different types of events to separate files:

```
logs/
├── application.log   # General application events, business logic, database operations
├── errors.log        # All error levels, security events, warnings
├── performance.log   # Performance metrics, slow operations, memory usage
└── requests.log      # HTTP requests and responses, API access logs
```

### Logging Features

#### Automatic Log Routing
- **Errors & Warnings**: Automatically routed to `errors.log`
- **Performance Metrics**: Automatically routed to `performance.log`
- **HTTP Requests/Responses**: Automatically routed to `requests.log`
- **Application Events**: Routed to `application.log`

#### Smart Response Logging
- **2xx responses**: Logged as INFO in requests.log
- **4xx/5xx responses**: Logged as WARNING in requests.log
- **Security events**: Also logged in errors.log

#### Performance Monitoring
- **Fast operations** (< 1s): Logged as INFO
- **Slow operations** (≥ 1s): Logged as WARNING
- **Memory usage**: Tracked for each request

### Custom Logging Methods

```php
use App\Infrastructure\Logging\LoggerInterface;

class MyService
{
    public function __construct(private LoggerInterface $logger) {}
    
    public function doSomething(): void
    {
        // Goes to application.log
        $this->logger->logBusinessEvent('Event created', ['id' => 123]);
        $this->logger->logDatabaseOperation('SELECT', ['table' => 'events']);
        
        // Goes to performance.log
        $this->logger->logPerformance('operation', 1.5, ['context' => 'data']);
        
        // Goes to requests.log
        $this->logger->logRequest('GET', '/api/endpoint', ['params' => 'value']);
        $this->logger->logResponse(200, 'GET', '/api/endpoint', ['duration' => 0.1]);
        
        // Goes to errors.log
        $this->logger->error('Something went wrong', ['error' => 'details']);
        $this->logger->logSecurityEvent('Unauthorized access', ['ip' => '127.0.0.1']);
    }
}
```

### Log Monitoring

```bash
# Monitor errors in real-time
tail -f logs/errors.log

# Check performance issues
grep "WARNING" logs/performance.log

# Monitor API access
tail -f logs/requests.log

# Debug application flow
tail -f logs/application.log
```

## API Documentation

### Available Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/events` | List all events with pagination |
| GET | `/events/{id}` | Get specific event by ID |
| GET | `/debug` | System debug information |
| GET | `/cache` | Cache statistics |
| GET | `/cache?action=clear` | Clear cache |

### Pagination Parameters

| Parameter | Type | Default | Range | Description |
|-----------|------|---------|-------|-------------|
| page | int | 1 | 1+ | Page number (1-based) |
| page_size | int | 10 | 1-100 | Items per page |
| sort_by | string | 'id' | id, event_name, location, created_at | Sort field |
| sort_direction | string | 'ASC' | ASC, DESC | Sort direction |

### Response Structure

#### Event List Response
```json
{
  "data": [
    {
      "id": 1,
      "event_name": "Festival de Fado",
      "location": "Lisboa",
      "latitude": 38.7223,
      "longitude": -9.1393,
      "created_at": "2024-01-15 09:00:00",
      "updated_at": "2024-01-15 09:00:00"
    }
  ],
  "pagination": {
    "current_page": 1,
    "page_size": 10,
    "total_items": 25,
    "total_pages": 3,
    "has_next_page": true,
    "has_previous_page": false,
    "next_page": 2,
    "previous_page": null,
    "start_item": 1,
    "end_item": 10
  }
}
```

#### Single Event Response
```json
{
  "id": 1,
  "event_name": "Festival de Fado",
  "location": "Lisboa",
  "latitude": 38.7223,
  "longitude": -9.1393,
  "created_at": "2024-01-15 09:00:00",
  "updated_at": "2024-01-15 09:00:00"
}
```

#### Error Response
```json
{
  "error": "Error message"
}
```

### HTTP Status Codes

| Status Code | Description | When Used |
|-------------|-------------|-----------|
| 200 OK | Success | Valid requests |
| 400 Bad Request | Invalid parameters | Invalid pagination, sort parameters, or IDs |
| 404 Not Found | Resource not found | Non-existent events or routes |
| 500 Internal Server Error | Server error | Unexpected errors |

## Docker Buildx (99% Faster Builds)

### Quick Start
```bash
# Build with Buildx (recommended)
docker buildx bake --load

# Traditional build (slower)
docker-compose build
```

### Performance Comparison
- **Traditional Build**: ~9 minutes
- **Buildx Build**: ~14 seconds
- **Improvement**: 99% faster

### Buildx Features
- **Multi-stage builds**: Optimized layer caching
- **Parallel processing**: Concurrent build stages
- **Cache optimization**: Intelligent dependency caching
- **Cross-platform**: ARM64 and AMD64 support

## Testing

### Test Statistics
- **Total Tests**: 151
- **Total Assertions**: 413
- **Coverage**: Comprehensive unit and integration tests
- **Quality**: PHPStan Level 8 (0 errors)

### Test Categories
- **Unit Tests**: Domain entities, value objects, services
- **Integration Tests**: Repository implementations, caching
- **Service Tests**: Application services, use cases
- **Controller Tests**: HTTP endpoints, response formatting

### Running Tests
```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage

# Run specific test file
vendor/bin/phpunit tests/Unit/Application/Service/EventServiceTest.php

# Run test suite
vendor/bin/phpunit tests/Unit/Domain/
```

## Development Tools

### Code Quality
```bash
# Static analysis (PHPStan Level 8)
composer analyse

# Code formatting (PHP-CS-Fixer)
composer cs-fix

# Code style check
composer cs-check

# Run all quality checks
composer quality

# Fix code style and run analysis
composer quality-fix
```

### Docker Development
```bash
# Build containers
docker buildx bake --load

# Start development environment
docker-compose up -d

# View application logs
docker-compose logs -f app

# Execute commands in container
docker-compose exec app composer install
```

## Production Deployment

### Optimization Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Use `LOG_LEVEL=WARNING` or `LOG_LEVEL=ERROR`
- [ ] Configure Redis caching (`CACHE_STRATEGY=redis`)
- [ ] Use rotating log files (`LOG_HANDLER=rotating`)
- [ ] Set up proper web server configuration
- [ ] Configure database connection pooling
- [ ] Set up monitoring and alerting

### Web Server Configuration

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/3cket_events/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## Container Information

### Docker Services
- **App**: PHP 8.2-FPM with all extensions
- **Database**: MySQL 8.0 with Portuguese events data
- **Redis**: Redis 7-alpine for caching
- **Webserver**: Nginx alpine serving on port 8000

### Port Mapping
- **HTTP**: `http://localhost:8000` (Nginx → PHP-FPM)
- **MySQL**: `localhost:3306` (for direct database access)
- **Redis**: `localhost:6379` (for cache management)

## Troubleshooting

### Common Issues

#### Port Already in Use
```bash
# Check what's using port 8000
lsof -i :8000

# Stop containers and restart
docker-compose down
docker-compose up -d
```

#### Database Connection Issues
```bash
# Check database logs
docker-compose logs db

# Verify database is running
docker-compose ps
```

#### Cache Issues
```bash
# Clear cache
curl http://localhost:8000/cache?action=clear

# Check Redis connection
docker-compose exec redis redis-cli ping
```

### Logs Location
- **Application logs**: `logs/` directory
- **Docker logs**: `docker-compose logs [service]`
- **Nginx logs**: Inside webserver container

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests: `composer test`
5. Run quality checks: `composer quality`
6. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support, please open an issue on the GitHub repository or contact the development team.

---

**Built with ❤️ in Portugal** 🇵🇹
