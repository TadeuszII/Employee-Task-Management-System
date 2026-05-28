--- USER
CREATE TABLE `employee_task_management_system`.`user` (
    `id` INT NOT NULL AUTO_INCREMENT , 
    `full_name` VARCHAR(50) NOT NULL ,
    `username` VARCHAR(50) NOT NULL , 
    `password` VARCHAR(255) NOT NULL , 
    `role` ENUM('admin','employee','','') 
    NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
      PRIMARY KEY (`id`)) ENGINE = InnoDB COMMENT = 'User information and role'; 


--- TASK
CREATE TABLE tasks ( 
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  assigned_to INT,
  STATUS ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (assigned_to) REFERENCES user(id) ON DELETE SET NULL
)

--- NOTIFICATIONS
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    recipient INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    is_read BOOLEAN DEFAULT FALSE
);