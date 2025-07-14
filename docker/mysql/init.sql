-- Create the events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample events in Portuguese municipalities
INSERT INTO events (id, event_name, location, latitude, longitude, created_at, updated_at) VALUES
(1, 'Festival de Fado', 'Lisboa', 38.7223, -9.1393, '2024-01-15 09:00:00', '2024-01-15 09:00:00'),
(2, 'Feira de Artesanato', 'Porto', 41.1579, -8.6291, '2024-01-20 10:00:00', '2024-01-20 10:00:00'),
(3, 'Concerto de Música Clássica', 'Coimbra', 40.2033, -8.4103, '2024-02-01 19:00:00', '2024-02-01 19:00:00'),
(4, 'Festival Gastronómico', 'Braga', 41.5454, -8.4265, '2024-02-10 12:00:00', '2024-02-10 12:00:00'),
(5, 'Mostra de Cinema', 'Aveiro', 40.6443, -2.8911, '2024-02-15 18:00:00', '2024-02-15 18:00:00'),
(6, 'Feira Medieval', 'Óbidos', 39.3606, -9.1571, '2024-02-20 14:00:00', '2024-02-20 14:00:00'),
(7, 'Festival de Verão', 'Faro', 37.0194, -7.9304, '2024-03-01 16:00:00', '2024-03-01 16:00:00'),
(8, 'Encontro de Folclore', 'Viana do Castelo', 41.6938, -8.8342, '2024-03-05 15:00:00', '2024-03-05 15:00:00'),
(9, 'Exposição de Arte', 'Viseu', 40.6566, -7.9139, '2024-03-10 10:00:00', '2024-03-10 10:00:00'),
(10, 'Festival de Jazz', 'Leiria', 39.7436, -8.8071, '2024-03-15 20:00:00', '2024-03-15 20:00:00'),
(11, 'Mercado de Natal', 'Guimarães', 41.4426, -8.2918, '2024-03-20 11:00:00', '2024-03-20 11:00:00'),
(12, 'Corrida Popular', 'Setúbal', 38.5244, -8.8882, '2024-03-25 08:00:00', '2024-03-25 08:00:00'),
(13, 'Festival de Teatro', 'Évora', 38.5664, -7.9077, '2024-04-01 19:30:00', '2024-04-01 19:30:00'),
(14, 'Feira de Livros', 'Santarém', 39.2369, -8.6871, '2024-04-05 09:00:00', '2024-04-05 09:00:00'),
(15, 'Concerto de Rock', 'Beja', 38.0150, -7.8632, '2024-04-10 21:00:00', '2024-04-10 21:00:00'),
(16, 'Mostra de Vinhos', 'Vila Real', 41.3006, -7.7443, '2024-04-15 17:00:00', '2024-04-15 17:00:00'),
(17, 'Festival de Dança', 'Castelo Branco', 39.8197, -7.4909, '2024-04-20 20:00:00', '2024-04-20 20:00:00'),
(18, 'Feira de Produtos Regionais', 'Portalegre', 39.2967, -7.4281, '2024-04-25 13:00:00', '2024-04-25 13:00:00'),
(19, 'Exposição de Fotografia', 'Bragança', 41.8071, -6.7571, '2024-05-01 14:00:00', '2024-05-01 14:00:00'),
(20, 'Festival de Música Popular', 'Guarda', 40.5364, -7.2683, '2024-05-05 18:00:00', '2024-05-05 18:00:00'),
(21, 'Concerto de Orquestra', 'Funchal', 32.6669, -16.9241, '2024-05-10 19:00:00', '2024-05-10 19:00:00'),
(22, 'Festival de Folclore', 'Angra do Heroísmo', 38.6551, -27.2208, '2024-05-15 16:00:00', '2024-05-15 16:00:00'),
(23, 'Mostra de Artesanato', 'Torres Vedras', 39.0910, -9.2584, '2024-05-20 10:00:00', '2024-05-20 10:00:00'),
(24, 'Festival de Verão', 'Caldas da Rainha', 39.4032, -9.1378, '2024-05-25 15:00:00', '2024-05-25 15:00:00'),
(25, 'Feira de Antiguidades', 'Lamego', 41.0968, -7.8096, '2024-05-30 11:00:00', '2024-05-30 11:00:00');

-- Create indexes for better performance

-- Basic indexes for common queries
CREATE INDEX idx_events_location ON events(location);
CREATE INDEX idx_events_event_name ON events(event_name);
CREATE INDEX idx_events_created_at ON events(created_at);
CREATE INDEX idx_events_updated_at ON events(updated_at);

-- Composite indexes for geographic searches
CREATE INDEX idx_events_coordinates ON events(latitude, longitude);

-- Full-text search index for text searches
CREATE FULLTEXT INDEX idx_events_fulltext ON events(event_name, location);

-- Composite indexes for common query patterns
CREATE INDEX idx_events_location_created ON events(location, created_at);
CREATE INDEX idx_events_name_location ON events(event_name, location);

-- Covering index for pagination queries (includes all commonly selected columns)
CREATE INDEX idx_events_pagination_covering ON events(id, event_name, location, latitude, longitude, created_at, updated_at);

-- Analyze tables for better query optimization
ANALYZE TABLE events; 