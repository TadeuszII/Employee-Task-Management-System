--- USER
CREATE TABLE `employee_task_management_system`.`user` (
    `id` INT NOT NULL AUTO_INCREMENT , 
    `full_name` VARCHAR(50) NOT NULL ,
    `username` VARCHAR(50) NOT NULL , 
    `password` VARCHAR(255) NOT NULL , 
    `role` ENUM('admin','employee','','') 
    NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
      PRIMARY KEY (`id`)) ENGINE = InnoDB COMMENT = 'User information and role';