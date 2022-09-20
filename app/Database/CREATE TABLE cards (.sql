CREATE TABLE cards (
     id int(5) unsigned NOT NULL AUTO_INCREMENT,
     background text NOT NULL,
     border text NOT NULL,
     card text NOT NULL,
     dna varchar(255) NOT NULL,
     clearDna text default null,
     sig text DEFAULT NULL,
     updated_at datetime DEFAULT NULL,
     created_at datetime DEFAULT CURRENT_TIMESTAMP,
     PRIMARY KEY (id),
     UNIQUE KEY dna (dna)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
