DROP TABLE IF EXISTS GROUPMEMBER CASCADE;
DROP TABLE IF EXISTS MEMBERRELATIONSHIP CASCADE;
DROP TABLE IF EXISTS POSTCOMMENT CASCADE;
DROP TABLE IF EXISTS POSTPRIVACY CASCADE;
DROP TABLE IF EXISTS MGROUP CASCADE;
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
    address VARCHAR(255) NOT NULL,
    internalEmailAddress VARCHAR(255) NOT NULL,
    associationID INT,
    FOREIGN KEY (associationID) REFERENCES ASSOCIATION(id) ON DELETE CASCADE
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
    groupOwner INT,
    groupInformation VARCHAR(5000),
    FOREIGN KEY (groupOwner) REFERENCES MEMBER(id)
);

CREATE TABLE GROUPMEMBER (
    memberID INT,
    groupID INT,
    FOREIGN KEY (memberID) REFERENCES MEMBER(id) ON DELETE CASCADE,
    FOREIGN KEY (groupID) REFERENCES MGROUP(id) ON DELETE CASCADE
);

CREATE TABLE POST (
    id INT PRIMARY KEY AUTO_INCREMENT,
    associationID INT,
    # viewOnly, viewAndComment, viewAndAddLink
    classification VARCHAR(32) NOT NULL,
    # systemWide, condoOwners, public, private, group, public
    privacy VARCHAR(32) NOT NULL
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