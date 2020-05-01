-- #! sqlite
-- #{ piggyfactions

-- # { factions

-- # { init
CREATE TABLE IF NOT EXISTS factions
(
    id          VARCHAR(36) PRIMARY KEY,
    name        VARCHAR(16) UNIQUE,
    description TEXT,
    motd        TEXT,
    members     TEXT,
    permissions TEXT,
    flags       TEXT,
    home        TEXT,
    relations   TEXT
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
INSERT INTO factions (id, name, members, permissions, flags)
VALUES (:id, :name, :members, :permissions, :flags);
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
UPDATE factions
SET name=:name,
    description=:description,
    motd=:motd,
    members=:members,
    permissions=:permissions,
    flags=:flags,
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
-- #    :faction ?string
-- #    :role ?string
-- #    :power float
INSERT INTO players (uuid, username, faction, role, power)
VALUES (:uuid, :username, :faction, :role, :power);
-- # }

-- # { update
-- #    :uuid string
-- #    :username string
-- #    :faction ?string
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
    id      INTEGER PRIMARY KEY,
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
-- #    :faction string
INSERT INTO claims (chunkX, chunkZ, level, faction)
VALUES (:chunkX, :chunkZ, :level, :faction);
-- # }

-- # { update
-- #    :id int
-- #    :faction string
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