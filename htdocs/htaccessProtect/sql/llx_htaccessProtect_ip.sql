CREATE TABLE IF NOT EXISTS llx_htaccessprotectip(
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255),
    ip VARCHAR(100),
    trusted BOOL NOT NULL DEFAULT '0',
    PRIMARY KEY (id)
);