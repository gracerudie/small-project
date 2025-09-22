-- Contacts Manager Minimal Schema
-- Purpose: Provides only the tables required by the app (Users, Contacts)
-- Safe to run on a fresh MySQL/MariaDB instance. If migrating an existing
-- database, see the MIGRATION section below instead of dropping tables.

-- Engine / charset chosen for modern UTF-8 support
CREATE DATABASE IF NOT EXISTS COP4331
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;
USE COP4331;

-- =====================================================================
-- DROP (Fresh Install Only)
-- =====================================================================
-- Comment these two lines out if you have existing data you wish to keep.
DROP TABLE IF EXISTS Contacts;
DROP TABLE IF EXISTS Users;

-- =====================================================================
-- Users Table
-- =====================================================================
-- Notes:
--  * Password length expanded to 255 for bcrypt/argon hashes.
--  * UNIQUE constraint on Login to prevent duplicates.
CREATE TABLE IF NOT EXISTS Users (
  ID INT NOT NULL AUTO_INCREMENT,
  FirstName VARCHAR(50) NOT NULL DEFAULT '',
  LastName  VARCHAR(50) NOT NULL DEFAULT '',
  Login     VARCHAR(50) NOT NULL DEFAULT '',
  Password  VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (ID),
  UNIQUE KEY ux_login (Login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================================
-- Contacts Table
-- =====================================================================
-- FOREIGN KEY enforces ownership; ON DELETE CASCADE removes a user's
-- contacts automatically if the user record is deleted.
CREATE TABLE IF NOT EXISTS Contacts (
  ID INT NOT NULL AUTO_INCREMENT,
  FirstName VARCHAR(50) NOT NULL DEFAULT '',
  LastName  VARCHAR(50) NOT NULL DEFAULT '',
  Phone     VARCHAR(50) NOT NULL DEFAULT '',
  Email     VARCHAR(50) NOT NULL DEFAULT '',
  UserID    INT NOT NULL,
  PRIMARY KEY (ID),
  KEY idx_user (UserID),
  CONSTRAINT fk_contacts_user FOREIGN KEY (UserID)
    REFERENCES Users(ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================================
-- MIGRATION (Apply to Existing Install Instead of Full Recreate)
-- =====================================================================
-- If you already have Users/Contacts without these constraints, run:
-- ALTER TABLE Users MODIFY Password VARCHAR(255) NOT NULL DEFAULT '';
-- ALTER TABLE Users ADD UNIQUE KEY ux_login (Login);
-- (Optional) Add FK if data is clean and all Contacts.UserID values exist:
--   ALTER TABLE Contacts ADD CONSTRAINT fk_contacts_user FOREIGN KEY (UserID)
--     REFERENCES Users(ID) ON DELETE CASCADE;

-- =====================================================================
-- OPTIONAL SEED (Insert a demo user) - Comment out if not needed
-- =====================================================================
-- INSERT INTO Users (FirstName, LastName, Login, Password)
-- VALUES ('Demo','User','demo', '$2y$10$exampleReplaceWithRealHash................................');

-- INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID)
-- VALUES ('Alice','Example','555-1111','alice@example.com', 1);

-- End of schema
