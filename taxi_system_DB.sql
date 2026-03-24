CREATE DATABASE taxi_system;
USE taxi_system;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE locations (
    location_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    distance_from_base_km FLOAT
);


INSERT INTO locations (name, distance_from_base_km) VALUES
("Mirpur", 2),
("Banani", 5),
("Gulshan", 6),
("Uttara", 10),
("Dhanmondi", 4),
("Motijheel", 7);

CREATE TABLE taxis (
    taxi_id INT AUTO_INCREMENT PRIMARY KEY,
    driver_name VARCHAR(100),
    plate_number VARCHAR(50),
    status ENUM('Available','Busy') DEFAULT 'Available'
);

INSERT INTO taxis (driver_name, plate_number, status) VALUES
("Rahim Uddin", "DHA-11-1234", "Available"),
("Karim Sheikh", "DHA-77-9876", "Busy"),
("Selim Miah", "DHA-44-5678", "Available"),
("Jamal Hossain", "DHA-22-4455", "Available");

CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    taxi_id INT,
    pickup_location_id INT,
    drop_location_id INT,
    distance_km FLOAT,
    fare FLOAT,
    booking_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (taxi_id) REFERENCES taxis(taxi_id),
    FOREIGN KEY (pickup_location_id) REFERENCES locations(location_id),
    FOREIGN KEY (drop_location_id) REFERENCES locations(location_id)
);

ALTER TABLE bookings
ADD COLUMN ride_status ENUM('pending', 'cancelled', 'completed')
NOT NULL DEFAULT 'pending';


