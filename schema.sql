CREATE DATABASE IF NOT EXISTS hotel_management;
USE hotel_management;

CREATE TABLE admin_users (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT NOW()
);

CREATE TABLE rooms (
  room_id INT AUTO_INCREMENT PRIMARY KEY,
  room_number VARCHAR(10) NOT NULL UNIQUE,
  room_type ENUM('Single','Double','Suite') NOT NULL,
  price_per_night DECIMAL(10,2) NOT NULL,
  status ENUM('Available','Occupied','Maintenance') 
         DEFAULT 'Available',
  description TEXT
);

CREATE TABLE guests (
  guest_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  mobile VARCHAR(10) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  address TEXT NOT NULL,
  id_proof VARCHAR(20) NOT NULL,
  created_at DATETIME DEFAULT NOW()
);

CREATE TABLE bookings (
  booking_id INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  guest_id INT NOT NULL,
  check_in_date DATE NOT NULL,
  check_out_date DATE NOT NULL,
  total_nights INT NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('Booked','Checked In',
              'Checked Out','Cancelled') 
         DEFAULT 'Booked',
  created_at DATETIME DEFAULT NOW(),
  FOREIGN KEY (room_id) 
    REFERENCES rooms(room_id),
  FOREIGN KEY (guest_id) 
    REFERENCES guests(guest_id)
);

CREATE TABLE payments (
  payment_id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  amount_paid DECIMAL(10,2) NOT NULL,
  payment_date DATETIME DEFAULT NOW(),
  payment_mode ENUM('Cash','Card','UPI') 
               DEFAULT 'Cash',
  status ENUM('Paid','Pending','Partial') 
         DEFAULT 'Paid',
  FOREIGN KEY (booking_id) 
    REFERENCES bookings(booking_id)
);

-- Default admin (password = Admin@123)
INSERT INTO admin_users (username, email, password_hash)
VALUES ('admin', 'admin@hotel.com',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample rooms
INSERT INTO rooms 
  (room_number, room_type, price_per_night, 
   status, description)
VALUES
  ('101', 'Single', 1500.00, 'Available', 
   'Single bed, AC, TV'),
  ('102', 'Single', 1500.00, 'Available', 
   'Single bed, AC, TV'),
  ('201', 'Double', 2500.00, 'Available', 
   'Double bed, AC, TV, Mini fridge'),
  ('202', 'Double', 2500.00, 'Occupied', 
   'Double bed, AC, TV, Mini fridge'),
  ('301', 'Suite',  5000.00, 'Available', 
   'King bed, AC, TV, Jacuzzi, Sea view'),
  ('302', 'Suite',  5000.00, 'Maintenance', 
   'King bed, AC, TV, Jacuzzi, Sea view');

-- Sample guests
INSERT INTO guests 
  (name, mobile, email, address, id_proof)
VALUES
  ('Rahul Sharma', '9823456781', 
   'rahul@gmail.com', 'Nashik', 'ABCD1234'),
  ('Priya Deshmukh', '9765432108', 
   'priya@gmail.com', 'Pune', 'EFGH5678'),
  ('Amit Kulkarni', '9887654321', 
   'amit@gmail.com', 'Mumbai', 'IJKL9012'),
  ('Sneha Joshi', '9654321078', 
   'sneha@gmail.com', 'Nashik', 'MNOP3456'),
  ('Vikram Naik', '9543210987', 
   'vikram@gmail.com', 'Aurangabad', 'QRST7890');

-- Sample bookings
INSERT INTO bookings
  (room_id, guest_id, check_in_date, 
   check_out_date, total_nights, 
   total_amount, status)
VALUES
  (4, 1, '2026-04-20', '2026-04-23', 3, 7500.00, 
   'Checked In'),
  (1, 2, '2026-04-25', '2026-04-27', 2, 3000.00, 
   'Booked'),
  (3, 3, '2026-04-15', '2026-04-18', 3, 7500.00, 
   'Checked Out');

-- Sample payments
INSERT INTO payments
  (booking_id, amount_paid, payment_mode, status)
VALUES
  (1, 7500.00, 'Cash', 'Paid'),
  (2, 1500.00, 'UPI', 'Partial'),
  (3, 7500.00, 'Card', 'Paid');
