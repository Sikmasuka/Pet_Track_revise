-- Step 1: Create the database
CREATE DATABASE pettrackdb;

-- Step 2: Select the database to use 
USE pettrackdb;

-- Step 3: Admin table
CREATE TABLE Admin (
    admin_username VARCHAR(50) PRIMARY KEY,
    admin_name VARCHAR(100),
    admin_password VARCHAR(100)
);
INSERT INTO Admin (admin_username, admin_name, admin_password) VALUES ('admin', 'admin', 'admin');


-- Step 4: Veterinarian table
CREATE TABLE Veterinarian (
    vet_id INT PRIMARY KEY AUTO_INCREMENT,
    vet_name VARCHAR(100),
    vet_contact_number VARCHAR(15),
    vet_username VARCHAR(50),
    vet_password VARCHAR(100)
);

-- Step 5: Client table
CREATE TABLE Client (
    client_id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(100),
    client_address TEXT,
    client_contact_number VARCHAR(15)
);

-- Step 6: Pet table
CREATE TABLE Pet (
    pet_id INT PRIMARY KEY AUTO_INCREMENT,
    pet_name VARCHAR(100),
    pet_sex VARCHAR(10),
    pet_weight DECIMAL(5,2),
    pet_breed VARCHAR(50),
    pet_birth_date DATE,
    client_id INT,
    FOREIGN KEY (client_id) REFERENCES Client(client_id)
);

-- Step 7: Medical Records table
CREATE TABLE Medical_Records (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    pet_id INT,
    date DATE,
    medical_condition TEXT,
    medical_diagnosis TEXT,
    medical_symptoms TEXT,
    medical_treatment TEXT,
    FOREIGN KEY (pet_id) REFERENCES Pet(pet_id)
);

-- Step 8: Reports table
CREATE TABLE Reports (
    record_id INT PRIMARY KEY,
    pet_id INT,
    client_id INT,
    vet_id INT,
    time_and_date DATETIME,
    FOREIGN KEY (record_id) REFERENCES Medical_Records(record_id),
    FOREIGN KEY (pet_id) REFERENCES Pet(pet_id),
    FOREIGN KEY (client_id) REFERENCES Client(client_id),
    FOREIGN KEY (vet_id) REFERENCES Veterinarian(vet_id)
);



-- Step 9: Sample Data
INSERT INTO Client (client_name, client_address, client_contact_number) VALUES
('John Doe', '123 Elm Street, Springfield', '555-1234'),
('Jane Smith', '456 Oak Avenue, Greenfield', '555-5678'),
('Alice Brown', '789 Pine Road, Rivertown', '555-8765'),
('Bob White', '321 Maple Drive, Lakedale', '555-4321'),
('Charlie Black', '654 Cedar Lane, Hilltop', '555-9876'),
('David Green', '987 Birch Way, Forest City', '555-6543'),
('Emily Blue', '246 Cherry Street, Westside', '555-3210'),
('Frank Red', '135 Walnut Avenue, Downtown', '555-2147'),
('Grace Yellow', '802 Aspen Road, Northfield', '555-3456'),
('Hannah Pink', '200 Spruce Lane, Fairview', '555-7654');


INSERT INTO Pet (pet_name, pet_sex, pet_weight, pet_breed, pet_birth_date, client_id) VALUES
('Max', 'Male', 12.5, 'Golden Retriever', '2018-03-15', 1),
('Bella', 'Female', 8.2, 'Labrador', '2017-05-22', 2),
('Rocky', 'Male', 5.0, 'Beagle', '2019-01-11', 3),
('Lucy', 'Female', 3.5, 'Shih Tzu', '2020-06-30', 4),
('Milo', 'Male', 4.8, 'Pug', '2021-04-10', 5),
('Luna', 'Female', 2.2, 'Chihuahua', '2020-08-09', 6),
('Oliver', 'Male', 10.0, 'Bulldog', '2016-12-12', 7),
('Daisy', 'Female', 6.3, 'Poodle', '2018-09-18', 8),
('Toby', 'Male', 7.4, 'Dachshund', '2021-02-25', 9),
('Coco', 'Female', 4.0, 'Cocker Spaniel', '2019-07-19', 10);


INSERT INTO Medical_Records (pet_id, date, medical_condition, medical_diagnosis, medical_symptoms, medical_treatment) VALUES
(1, '2023-01-15', 'Ear Infection', 'Otitis externa', 'Scratching ears, shaking head', 'Antibiotic ear drops'),
(1, '2023-04-05', 'Dental Issue', 'Periodontal disease', 'Bad breath, drooling', 'Dental cleaning, antibiotics'),
(2, '2023-02-20', 'Skin Allergy', 'Atopic dermatitis', 'Itchy skin, hair loss', 'Steroid ointment'),
(2, '2023-03-10', 'Obesity', 'Overweight', 'Difficulty moving, lethargy', 'Diet adjustment, increased exercise'),
(3, '2023-03-25', 'Arthritis', 'Degenerative joint disease', 'Limping, stiffness', 'Pain relief medication'),
(3, '2023-06-05', 'Eye Infection', 'Conjunctivitis', 'Red eyes, discharge', 'Antibiotic eye drops'),
(4, '2023-05-10', 'Ear Infection', 'Otitis externa', 'Scratching ears, head shaking', 'Antibiotic ear drops'),
(4, '2023-07-01', 'Allergy', 'Seasonal allergy', 'Itchy eyes, sneezing', 'Antihistamines'),
(5, '2023-01-11', 'Injury', 'Sprained ankle', 'Limping, swelling', 'Rest, anti-inflammatory medication'),
(5, '2023-05-17', 'Skin Infection', 'Bacterial pyoderma', 'Red spots, itching', 'Topical antibiotics'),
(6, '2023-03-30', 'Dehydration', 'Heatstroke', 'Excessive panting, weakness', 'Fluids, rest'),
(6, '2023-06-20', 'Flea Infestation', 'Flea dermatitis', 'Itchy skin, hair loss', 'Flea treatment, medicated shampoo'),
(7, '2023-02-12', 'Ear Infection', 'Otitis externa', 'Shaking head, scratching', 'Antibiotic ear drops'),
(7, '2023-05-30', 'Obesity', 'Overweight', 'Difficulty moving, lethargy', 'Diet change, exercise program'),
(8, '2023-04-01', 'Respiratory Issue', 'Bronchitis', 'Coughing, wheezing', 'Cough suppressant, antibiotics'),
(8, '2023-07-03', 'Infection', 'Upper respiratory infection', 'Cough, nasal discharge', 'Antibiotics'),
(9, '2023-01-25', 'Parasite Infestation', 'Heartworm', 'Coughing, lethargy', 'Heartworm treatment'),
(9, '2023-06-25', 'Injury', 'Fractured leg', 'Limping, swelling', 'Casting, pain relief'),
(10, '2023-02-18', 'Diarrhea', 'Gastroenteritis', 'Frequent diarrhea, vomiting', 'Fluids, anti-diarrheal medications'),
(10, '2023-05-15', 'Allergy', 'Food allergy', 'Vomiting, itching', 'Hypoallergenic diet'),
(10, '2023-08-12', 'Ear Infection', 'Otitis externa', 'Scratching ears, shaking head', 'Ear drops, oral antibiotics');


-- Create Table: payment_methods
CREATE TABLE payment_methods (
    method_id INT AUTO_INCREMENT PRIMARY KEY,
    method_name VARCHAR(50) NOT NULL
);

-- ✅ INSERT INTO payment_methods:
INSERT INTO payment_methods (method_name) VALUES
('Cash'),
('GCash'),
('Credit Card'),
('Bank Transfer');


-- Create payments Table with method_id as FOREIGN KEY
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    method_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (method_id) REFERENCES payment_methods(method_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

-- Example Insert into payments:
INSERT INTO payments (client_name, method_id, amount, description, date)
VALUES (
    'Danreb B. Salvacion',
    1,
    1500.00,
    'I want to pay it with cash.',
    '2025-07-09 22:23:07'
);

CREATE TABLE Logs (
    Log_ID INT AUTO_INCREMENT PRIMARY KEY,  -- Unique log entry identifier
    User_ID INT NOT NULL,                   -- References admin_id or vet_id (depending on role)
    Action_Type VARCHAR(100) NOT NULL,      -- The type of action (e.g., 'Login', 'Add Pet', 'Update Record')
    Table_Affected ENUM('Admin', 'Veterinarian') NOT NULL,  -- Indicates whether the user is an Admin or Veterinarian
    Description TEXT NOT NULL,              -- Human-readable description of the action
    Timestamp DATETIME DEFAULT CURRENT_TIMESTAMP  -- Time when the action occurred (auto set)
);