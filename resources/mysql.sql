-- #! mysql
-- #{ piggyfactions

-- # { factions

-- # { init
CREATE TABLE IF NOT EXISTS factions
(
    id            VARCHAR(36) PRIMARY KEY,
    name          TEXT,
    creation_time BIGINT,
    description   TEXT,
    motd          TEXT,
    members       JSON,
    permissions   JSON,
    flags         JSON,
    home          JSON,
    relations     JSON,
    banned        JSON,
    money         FLOAT DEFAULT 0,
    powerboost    FLOAT DEFAULT 0
);
-- # }

-- # { load
SELECT *
FROM factions;
-- # }

-- # { create
-- #    :id string
-- #    :name string
-- #    :members string
-- #    :permissions string
-- #    :flags string
INSERT INTO factions (id, name, creation_time, members, permissions, flags)
VALUES (:id, :name, UNIX_TIMESTAMP(), :members, :permissions, :flags);
-- # }

-- # { delete
-- #    :id string
DELETE
FROM factions
WHERE id = :id;
-- # }

-- # { update
-- #    :id string
-- #    :name string
-- #    :description ?string
-- #    :motd ?string
-- #    :members string
-- #    :permissions string
-- #    :flags string
-- #    :home ?string
-- #    :relations string
-- #    :banned string
-- #    :money float
-- #    :powerboost float
UPDATE factions
SET name=:name,
    description=:description,
    motd=:motd,
    members=:members,
    permissions=:permissions,
    flags=:flags,
    home=:home,
    relations=:relations,
    banned=:banned,
    money=:money,
    powerboost=:powerboost
WHERE id = :id;
-- # }

-- # }

-- # { players

-- # { init
CREATE TABLE IF NOT EXISTS players
(
    uuid     VARCHAR(36) PRIMARY KEY,
    username VARCHAR(16),
    faction  VARCHAR(36),
    role     TEXT,
    power    FLOAT,
    powerboost FLOAT DEFAULT 0,
    language VARCHAR(255) DEFAULT 'english'
);
-- # }

-- # { load
SELECT *
FROM players;
-- # }

-- # { create
-- #    :uuid string
-- #    :username string
-- #    :faction ?string
-- #    :role ?string
-- #    :power float
-- #    :language string
INSERT INTO players (uuid, username, faction, role, power, language)
VALUES (:uuid, :username, :faction, :role, :power, :language);
-- # }

-- # { update
-- #    :uuid string
-- #    :username string
-- #    :faction ?string
-- #    :role ?string
-- #    :power float
-- #    :powerboost float
-- #    :language string
UPDATE players
SET username=:username,
    faction=:faction,
    role=:role,
    power=:power,
    powerboost=:powerboost,
    language=:language
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
    faction VARCHAR(36)
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
-- #    :faction string
INSERT INTO claims (chunkX, chunkZ, level, faction)
VALUES (:chunkX, :chunkZ, :level, :faction);
-- # }

-- # { update
-- #    :chunkX int
-- #    :chunkZ int
-- #    :level string
-- #    :faction string
UPDATE claims
SET faction=:faction
WHERE chunkX = :chunkX
  AND chunkZ = :chunkZ
  AND level = :level;
-- # }

-- # { delete
-- #    :chunkX int
-- #    :chunkZ int
-- #    :level string
DELETE
FROM claims
WHERE chunkX = :chunkX
  AND chunkZ = :chunkZ
  AND level = :level;
-- # }

-- # }

-- # { logs

-- # { init
CREATE TABLE IF NOT EXISTS logs
(
    id        INTEGER PRIMARY KEY AUTO_INCREMENT,
    faction   VARCHAR(36),
    action    TEXT,
    timestamp INTEGER,
    data      TEXT
);
-- # }

-- # { loadall
-- #    :faction string
-- #    :count int
-- #    :startpoint int
SELECT data, action, timestamp
FROM logs
WHERE faction = :faction
LIMIT :count OFFSET :startpoint;
-- # }

-- # { countall
-- #    :faction string
SELECT *
FROM logs
WHERE faction = :faction;
-- # }

-- # { count
-- #    :faction string
-- #    :action string
SELECT *
FROM logs
WHERE action = :action
  AND faction = :faction;
-- # }

-- # { load
-- #    :action string
-- #    :faction string
-- #    :count int
-- #    :startpoint int
SELECT data, timestamp, action
FROM logs
WHERE action = :action
  AND faction = :faction
LIMIT :count OFFSET :startpoint;
-- # }

-- # { create
-- #    :faction string
-- #    :action string
-- #    :timestamp int
-- #    :data string
INSERT INTO logs (faction, action, timestamp, data)
VALUES (:faction, :action, :timestamp, :data);
-- # }

-- # { delete
-- #    :id int
DELETE
FROM logs
WHERE id = :id;
-- # }

-- # }

-- # { patches

-- # { 0
ALTER TABLE factions
    ADD money FLOAT DEFAULT 0;
-- # }

-- # { 1
ALTER TABLE players
    ADD language VARCHAR(255) DEFAULT 'english';
-- # }

-- # { 2
ALTER TABLE factions
    ADD powerboost FLOAT DEFAULT 0;
-- # }

-- # { 3
ALTER TABLE players
    ADD powerboost FLOAT DEFAULT 0;
-- # }

-- # }

-- # }