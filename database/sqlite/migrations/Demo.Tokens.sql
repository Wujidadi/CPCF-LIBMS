CREATE TABLE Tokens (
    Nickname   VARCHAR(128)  NOT NULL  PRIMARY KEY,
    ID         VARCHAR(24)   NOT NULL  UNIQUE,
    Token      VARCHAR(70)   NOT NUll  UNIQUE,
    CreatedAt  DATETIME      NOT NULL,
    UpdatedAt  DATETIME      NOT NULL
);
