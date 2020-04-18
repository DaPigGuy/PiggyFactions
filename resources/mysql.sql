-- #! mysql
-- #{ piggyfactions

-- # { factions

-- # { init
CREATE TABLE IF NOT EXISTS factions
(
    id          INTEGER PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(16) UNIQUE,
    leader      VARCHAR(36),
    description TEXT,
    motd        TEXT,
    members     JSON,
    permissions JSON,
    home        JSON,
    relations   JSON
);
-- # }

-- # { load
SELECT *
FROM factions;
-- # }

-- # { create
-- #    :name string
-- #    :leader string
-- #    :members string
-- #    :permissions string
INSERT INTO factions (name, leader, members, permissions)
VALUES (:name, :leader, :members, :permissions);
-- # }

-- # { delete
-- #    :id int
DELETE
FROM factions
WHERE id = :id;
-- # }

-- # { update
-- #    :id int
-- #    :name string
-- #    :leader string
-- #    :description ?string
-- #    :motd ?string
-- #    :members string
-- #    :permissions string
-- #    :home ?string
-- #    :relations string
UPDATE factions
SET name=:name,
    leader=:leader,
    description=:description,
    motd=:motd,
    members=:members,
    permissions=:permissions,
    home=:home,
    relations=:relations
WHERE id = :id;
-- # }

-- # }

-- # { players

-- # { init
CREATE TABLE IF NOT EXISTS players
(
    uuid     VARCHAR(36) PRIMARY KEY UNIQUE,
    username VARCHAR(16) UNIQUE,
    faction  INTEGER,
    role     TEXT,
    power    FLOAT
);
-- # }

-- # { load
SELECT *
FROM players;
-- # }

-- # { create
-- #    :uuid string
-- #    :username string
-- #    :faction ?int
-- #    :role ?string
-- #    :power float
INSERT INTO players (uuid, username, faction, role, power)
VALUES (:uuid, :username, :faction, :role, :power);
-- # }

-- # { update
-- #    :uuid string
-- #    :username string
-- #    :faction ?int
-- #    :role ?string
-- #    :power float
UPDATE players
SET username=:username,
    faction=:faction,
    role=:role,
    power=:power
WHERE uuid = :uuid;
-- # }

-- # }

-- # { claims

-- # { init
CREATE TABLE IF NOT EXISTS claims
(
    id      INTEGER PRIMARY KEY AUTO_INCREMENT,
    chunkX  INTEGER,
    chunkZ  INTEGER,
    level   TEXT,
    faction INTEGER
);
-- # }

-- # { load
SELECT *
FROM claims;
-- # }

-- # { create
-- #    :chunkX int
-- #    :chunkZ int
-- #    :level string
-- #    :faction int
INSERT INTO claims (chunkX, chunkZ, level, faction)
VALUES (:chunkX, :chunkZ, :level, :faction);
-- # }

-- # { update
-- #    :id int
-- #    :faction int
UPDATE claims
SET faction=:faction
WHERE id = :id;
-- # }

-- # { delete
-- #    :id int
DELETE
FROM claims
WHERE id = :id;
-- # }

-- # }

-- # }