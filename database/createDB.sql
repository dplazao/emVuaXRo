DROP TABLE IF EXISTS CONDO CASCADE;
DROP TABLE IF EXISTS BUILDING CASCADE;
DROP TABLE IF EXISTS GROUPMEMBER CASCADE;
DROP TABLE IF EXISTS MEMBERRELATIONSHIP CASCADE;
DROP TABLE IF EXISTS POSTCOMMENT CASCADE;
DROP TABLE IF EXISTS POSTPRIVACY CASCADE;
DROP TABLE IF EXISTS MGROUP CASCADE;
DROP TABLE IF EXISTS ASSOCIATIONOWNER CASCADE;
DROP TABLE IF EXISTS MEMBER CASCADE;
DROP TABLE IF EXISTS ASSOCIATION CASCADE;
DROP TABLE IF EXISTS POST CASCADE;

CREATE TABLE ASSOCIATION (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE MEMBER (
    id INT PRIMARY KEY AUTO_INCREMENT,
    privilege VARCHAR(32) NOT NULL,
    status VARCHAR(32) NOT NULL,
    password VARCHAR(255),
    remember_token VARCHAR(255),
    mustChangePassword BOOL NOT NULL DEFAULT FALSE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    internalEmailAddress VARCHAR(255) NOT NULL,
    associationID INT,
    FOREIGN KEY (associationID) REFERENCES ASSOCIATION(id) ON DELETE CASCADE
);

CREATE TABLE ASSOCIATIONOWNER (
    associationID INT NOT NULL,
    memberID INT NOT NULL,
    FOREIGN KEY (associationID) REFERENCES ASSOCIATION(id) ON DELETE CASCADE,
    FOREIGN KEY (memberID) REFERENCES MEMBER(id) ON DELETE CASCADE
);

CREATE TABLE BUILDING (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    associationID INT NOT NULL,
    spaceFee FLOAT NOT NULL DEFAULT 1,
    FOREIGN KEY (associationID) REFERENCES ASSOCIATION(id) ON DELETE CASCADE
);

CREATE TABLE CONDO (
    id INT PRIMARY KEY AUTO_INCREMENT,
    buildingID INT NOT NULL,
    ownerID INT,
    parkingSpaces INT NOT NULL DEFAULT 0,
    storageSpace INT NOT NULL DEFAULT 0,
    FOREIGN KEY (buildingID) REFERENCES BUILDING(id) ON DELETE CASCADE,
    FOREIGN KEY (ownerID) REFERENCES MEMBER(id) ON DELETE SET NULL
);

CREATE TABLE MEMBERRELATIONSHIP (
    memberID INT NOT NULL,
    type VARCHAR(32) NOT NULL,
    withMemberID INT NOT NULL,
    FOREIGN KEY (memberID) REFERENCES MEMBER(id) ON DELETE CASCADE,
    FOREIGN KEY (withMemberID) REFERENCES MEMBER(id) ON DELETE CASCADE
);

CREATE TABLE MGROUP (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    owner INT,
    information VARCHAR(5000),
    FOREIGN KEY (owner) REFERENCES MEMBER(id)
);

CREATE TABLE GROUPMEMBER (
    memberID INT,
    groupID INT,
    accepted BOOL DEFAULT FALSE,
    UNIQUE (memberID, groupID),
    FOREIGN KEY (memberID) REFERENCES MEMBER(id) ON DELETE CASCADE,
    FOREIGN KEY (groupID) REFERENCES MGROUP(id) ON DELETE CASCADE
);

CREATE TABLE POST (
    id INT PRIMARY KEY AUTO_INCREMENT,
    postName VARCHAR(255) NOT NULL,
    memberID INT,
    postText TEXT,
    postPicture VARCHAR(255),
    associationID INT,
    # viewOnly, viewAndComment, viewAndAddLink
    classification VARCHAR(32) NOT NULL,
    # systemWide, condoOwners, public, private, group
    privacy VARCHAR(32) NOT NULL,
    FOREIGN KEY (memberID) REFERENCES MEMBER(id) ON DELETE CASCADE
);

CREATE TABLE POSTCOMMENT (
    id INT PRIMARY KEY AUTO_INCREMENT,
    onPostID INT NOT NULL,
    content VARCHAR(2000) NOT NULL,
    FOREIGN KEY (onPostID) REFERENCES POST(id) ON DELETE CASCADE
);

CREATE TABLE POSTPRIVACY (
    postID INT NOT NULL,
    # groupID or memberID
    groupID INT,
    memberID INT,
    FOREIGN KEY (postID) REFERENCES POST(id) ON DELETE CASCADE,
    FOREIGN KEY (groupID) REFERENCES MGROUP(id) ON DELETE CASCADE,
    FOREIGN KEY (memberID) REFERENCES MEMBER(id) ON DELETE CASCADE
);

# Password, bcrypt/argon "$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6"
# "password123"

INSERT INTO ASSOCIATION (id, name) VALUES (1, 'SYSTEM');
INSERT INTO ASSOCIATION (id, name) VALUES (2, 'First association');

INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (1, 1, 'admin', 'Admin User', '75685 Macpherson Parkway', 'sysadmin', 'active', 'admin@db.condo', '$2y$10$gjyY35KYVbtO9vwn33VQjuWNOfgm2r.uiLXFOcuhLk/l.MRjdgtH.');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (2, 2, 'admin@condo2.com', 'Cchaddie', '8 Russell Street', 'admin', 'active', 'admin@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (3, 2, 'diego@plazao.ca', 'Diego', '442 Forest Drive', 'owner', 'active', 'Diego@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (4, 2, 'diego2@plazao.ca', 'Diego2', '3464 Veith Road', 'owner', 'active', 'Diego2@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (5, 2, 'csaterthwait4@bloomberg.com', 'Collin', '9 Pawling Circle', 'owner', 'active', 'Collin@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (6, 2, 'ltustin5@hud.gov', 'Lenee', '640 Lakeland Hill', 'owner', 'active', 'Lenee@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (7, 2, 'kcratchley6@list-manage.com', 'Kimberlyn', '38184 Hermina Avenue', 'owner', 'active', 'Kimberlyn@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (8, 2, 'bboles7@va.gov', 'Barbette', '70965 Sugar Court', 'owner', 'active', 'Barbette@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (9, 2, 'gforcer8@behance.net', 'Glynnis', '5834 Fieldstone Avenue', 'admin', 'active', 'Glynnis@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (10, 2, 'bbuick9@whitehouse.gov', 'Belva', '891 Del Sol Way', 'owner', 'active', 'Belva@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (11, 2, 'wmarcoa@gov.uk', 'Wyndham', '7 Esch Hill', 'owner', 'active', 'Wyndham@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (12, 2, 'elepiscopiob@nature.com', 'Ely', '98 Ridgeview Way', 'owner', 'active', 'Ely@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (13, 2, 'fstudholmec@imdb.com', 'Florian', '840 Lien Pass', 'owner', 'active', 'Florian@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (14, 2, 'gbeadnalld@google.cn', 'Gertrudis', '65 Heath Avenue', 'owner', 'active', 'Gertrudis@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (15, 2, 'bregardsoee@vimeo.com', 'Brad', '334 East Pass', 'owner', 'active', 'Brad@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (16, 2, 'sgatherellf@storify.com', 'Silva', '76 Eastlawn Center', 'owner', 'active', 'Silva@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (17, 2, 'rtrippettg@jiathis.com', 'Rosamund', '9 Clyde Gallagher Circle', 'owner', 'active', 'Rosamund@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (18, 2, 'tgligorijevich@networksolutions.com', 'Teddy', '0 Susan Park', 'owner', 'active', 'Teddy@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (19, 2, 'sduckeri@live.com', 'Sally', '3 Pierstorff Park', 'owner', 'active', 'Sally@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');
INSERT INTO MEMBER (id, associationID, email, name, address, privilege, status, internalEmailAddress, password) VALUES (20, 2, 'jtilnej@google.it', 'Joline', '4368 Eggendart Terrace', 'owner', 'active', 'Joline@2.condo', '$2y$10$s3lOybxwLra9dozw4nimZumQvd9NPYfUko3J8OhLV1QWsUfCLWEo6');

INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (2, 'friend', 3);
INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (2, 'friend', 4);
INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (2, 'friend', 5);
INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (2, 'friend', 6);
INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (3, 'friend', 6);
INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (3, 'family', 7);
INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (3, 'family', 8);
INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (3, 'colleague', 9);
INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (3, 'friend', 10);
INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (4, 'friend', 11);
INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (4, 'family', 12);
INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (4, 'friend', 13);

INSERT INTO ASSOCIATIONOWNER (associationID, memberID) VALUES (1, 1);
INSERT INTO ASSOCIATIONOWNER (associationID, memberID) VALUES (2, 2);
INSERT INTO ASSOCIATIONOWNER (associationID, memberID) VALUES (2, 9);

INSERT INTO BUILDING (id, name, associationID, spaceFee) VALUES (1, 'Red Building', 2, 10);

INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (1, 2, 1, 10);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (1, 3, 1, 10);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (1, 4, 2, 20);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (1, 5, 1, 10);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (1, 6, 3, 50);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (1, 7, 1, 10);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (1, 8, 1, 30);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (1, 9, 2, 10);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (1, 10, 1, 5);

INSERT INTO BUILDING (id, name, associationID, spaceFee) VALUES (2, 'Blue Building', 2, 15);

INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (2, 11, 2, 5);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (2, 12, 1, 10);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (2, 13, 3, 15);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (2, 14, 1, 10);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (2, 15, 1, 5);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (2, 16, 3, 10);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (2, 17, 1, 30);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (2, 18, 2, 10);
INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace) VALUES (2, 19, 1, 15);

INSERT INTO MGROUP (id, name, owner, information) VALUES (1, 'First group', 3, 'the cool group');
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (3, 1, TRUE);
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (4, 1, TRUE);
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (5, 1, TRUE);
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (6, 1, TRUE);
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (8, 1, TRUE);
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (12, 1, TRUE);

INSERT INTO MGROUP (id, name, owner, information) VALUES (2, 'Second group', 4, 'the cooler group');
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (4, 2, TRUE);
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (20, 2, TRUE);
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (16, 2, TRUE);
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (17, 2, TRUE);
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (5, 2, TRUE);
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (9, 2, TRUE);

INSERT INTO MGROUP (id, name, owner, information) VALUES (3, 'Lonely Group', 18, 'the cooler group');
INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (18, 3, TRUE);


INSERT INTO POST (id, postName, memberID, postText, associationID, classification, privacy) VALUES (1, 'Breathtaking Condo', 3, 'The condo is located along a mountain cliff', 2, 'viewAndComment', 'systemWide');
INSERT INTO POST (id, postName, memberID, postText, associationID, classification, privacy) VALUES (2, 'Moderate Condo', 4, 'This stylish residence is nestled on a large level block in a desirably tranquil cul-de-sac location. The house comes complete with two living rooms, a welcoming kitchen/dining area, two bathrooms, four bedrooms, a study, and a laundry, and retains the value of peaceful living while being conveniently close to shops, school and transport.', 2, 'viewOnly', 'systemWide');
INSERT INTO POST (id, postName, memberID, postText, associationID, classification, privacy) VALUES (3, 'Downtown Condo', 3, 'Located in the heart of the city', 2, 'viewAndComment', 'systemWide');
INSERT INTO POST (id, postName, memberID, postText, associationID, classification, privacy) VALUES (4, 'Border Downtown Condo', 2, "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.", 2, 'viewAndComment', 'public')
