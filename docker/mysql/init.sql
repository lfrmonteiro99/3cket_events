-- Create events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert seed data from the original CSV file
INSERT INTO events (name, location, latitude, longitude) VALUES
('Event1', 'Porto', 41.1621376, -8.6569731),
('Event2', 'Lisboa', 38.7243148, -9.1499468),
('Event3', 'Porto', 41.1294885, -8.6179528),
('Event3', 'Lisboa', 38.7343815, -9.1442348);

-- Create indexes for better performance
CREATE INDEX idx_events_location ON events(location);
CREATE INDEX idx_events_coordinates ON events(latitude, longitude); 