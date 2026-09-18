<?php
/**
 * db.php
 * SQLite database matching the HarvestHub schema (Section XI), expanded
 * to cover the tables needed for the Admin, Staff, and Customer
 * dashboards: accounts, plots, applications, resources, and crop logs.
 * Swapping the DSN below for a MySQL DSN is the only change needed to
 * move to the production stack described in Section IX.
 */

function getDb(): PDO {
    $dbPath = __DIR__ . '/data/harvesthub.sqlite';
    $isNew = !file_exists($dbPath);

    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');

    if ($isNew) {
        seedDatabase($pdo);
    }

    return $pdo;
}

function seedDatabase(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE SYSTEM_ADMINISTRATOR (
            AdminID INTEGER PRIMARY KEY AUTOINCREMENT,
            Name TEXT NOT NULL,
            Email TEXT NOT NULL UNIQUE,
            PasswordHash TEXT NOT NULL
        );

        CREATE TABLE GARDEN_COORDINATOR (
            CoordID INTEGER PRIMARY KEY AUTOINCREMENT,
            Name TEXT NOT NULL,
            Email TEXT NOT NULL UNIQUE,
            PasswordHash TEXT NOT NULL,
            Shift TEXT NOT NULL DEFAULT 'Morning'
        );

        CREATE TABLE COMMUNITY_GARDENER (
            GardenerID INTEGER PRIMARY KEY AUTOINCREMENT,
            Name TEXT NOT NULL,
            Email TEXT NOT NULL UNIQUE,
            PasswordHash TEXT NOT NULL
        );

        CREATE TABLE PLOT (
            PltID INTEGER PRIMARY KEY AUTOINCREMENT,
            Label TEXT NOT NULL,
            GardenerID INTEGER,
            Status TEXT NOT NULL DEFAULT 'Available',
            FOREIGN KEY (GardenerID) REFERENCES COMMUNITY_GARDENER(GardenerID)
        );

        CREATE TABLE PLOT_APPLICATION (
            AppID INTEGER PRIMARY KEY AUTOINCREMENT,
            GardenerID INTEGER NOT NULL,
            CoordID INTEGER,
            PltID INTEGER NOT NULL,
            Status TEXT NOT NULL DEFAULT 'Pending',
            AppliedAt TEXT NOT NULL DEFAULT (datetime('now')),
            FOREIGN KEY (GardenerID) REFERENCES COMMUNITY_GARDENER(GardenerID),
            FOREIGN KEY (CoordID) REFERENCES GARDEN_COORDINATOR(CoordID),
            FOREIGN KEY (PltID) REFERENCES PLOT(PltID)
        );

        CREATE TABLE CROP_LOG (
            LogID INTEGER PRIMARY KEY AUTOINCREMENT,
            GardenerID INTEGER NOT NULL,
            PltID INTEGER NOT NULL,
            CropName TEXT NOT NULL,
            MaintenanceNotes TEXT,
            HarvestYield TEXT,
            LoggedAt TEXT NOT NULL DEFAULT (datetime('now')),
            FOREIGN KEY (GardenerID) REFERENCES COMMUNITY_GARDENER(GardenerID),
            FOREIGN KEY (PltID) REFERENCES PLOT(PltID)
        );

        CREATE TABLE RESOURCE (
            ResourceID INTEGER PRIMARY KEY AUTOINCREMENT,
            Name TEXT NOT NULL,
            TotalQty INTEGER NOT NULL,
            AvailableQty INTEGER NOT NULL
        );

        CREATE TABLE RESOURCE_TXN (
            TxnID INTEGER PRIMARY KEY AUTOINCREMENT,
            GardenerID INTEGER NOT NULL,
            CoordID INTEGER,
            ResourceID INTEGER NOT NULL,
            Qty INTEGER NOT NULL,
            Status TEXT NOT NULL DEFAULT 'Requested',
            RequestedAt TEXT NOT NULL DEFAULT (datetime('now')),
            FOREIGN KEY (GardenerID) REFERENCES COMMUNITY_GARDENER(GardenerID),
            FOREIGN KEY (CoordID) REFERENCES GARDEN_COORDINATOR(CoordID),
            FOREIGN KEY (ResourceID) REFERENCES RESOURCE(ResourceID)
        );

        CREATE TABLE EXCHANGE_LISTING (
            ListingID INTEGER PRIMARY KEY AUTOINCREMENT,
            GardenerID INTEGER NOT NULL,
            Crop TEXT NOT NULL,
            Qty INTEGER NOT NULL,
            Notes TEXT,
            CreatedAt TEXT NOT NULL DEFAULT (datetime('now')),
            FOREIGN KEY (GardenerID) REFERENCES COMMUNITY_GARDENER(GardenerID)
        );

        CREATE TABLE EXCHANGE_ORDER (
            OrderID INTEGER PRIMARY KEY AUTOINCREMENT,
            ListingID INTEGER NOT NULL,
            GardenerID INTEGER NOT NULL,
            ClaimedAt TEXT NOT NULL DEFAULT (datetime('now')),
            FOREIGN KEY (ListingID) REFERENCES EXCHANGE_LISTING(ListingID),
            FOREIGN KEY (GardenerID) REFERENCES COMMUNITY_GARDENER(GardenerID)
        );
    ");

    $hash = password_hash('demo1234', PASSWORD_BCRYPT);

    // --- Accounts ---
    $pdo->prepare("INSERT INTO SYSTEM_ADMINISTRATOR (Name, Email, PasswordHash) VALUES (?, ?, ?)")
        ->execute(['Ana Bautista', 'admin@harvesthub.test', $hash]);

    $insertCoord = $pdo->prepare("INSERT INTO GARDEN_COORDINATOR (Name, Email, PasswordHash, Shift) VALUES (?, ?, ?, ?)");
    $insertCoord->execute(['Ramon Cruz', 'coordinator@harvesthub.test', $hash, 'Morning']);

    $insertGardener = $pdo->prepare("INSERT INTO COMMUNITY_GARDENER (Name, Email, PasswordHash) VALUES (?, ?, ?)");
    $gardeners = [
        ['Maria Santos', 'maria@harvesthub.test'],
        ['Jun Dela Cruz', 'jun@harvesthub.test'],
        ['Liza Ramos', 'liza@harvesthub.test'],
    ];
    foreach ($gardeners as $g) {
        $insertGardener->execute([$g[0], $g[1], $hash]);
    }

    // --- Plots (some assigned, some available) ---
    $insertPlot = $pdo->prepare("INSERT INTO PLOT (Label, GardenerID, Status) VALUES (?, ?, ?)");
    $insertPlot->execute(['Plot A1', 1, 'Occupied']);
    $insertPlot->execute(['Plot A2', 2, 'Occupied']);
    $insertPlot->execute(['Plot A3', null, 'Available']);
    $insertPlot->execute(['Plot B1', null, 'Available']);
    $insertPlot->execute(['Plot B2', null, 'Available']);

    // --- A pending application, so the Staff dashboard has something to act on ---
    $pdo->prepare("INSERT INTO PLOT_APPLICATION (GardenerID, PltID, Status) VALUES (?, ?, 'Pending')")
        ->execute([3, 3]); // Liza applying for Plot A3

    // --- Crop logs for gardeners who already have plots ---
    $insertLog = $pdo->prepare("INSERT INTO CROP_LOG (GardenerID, PltID, CropName, MaintenanceNotes, HarvestYield) VALUES (?, ?, ?, ?, ?)");
    $insertLog->execute([1, 1, 'Tomatoes', 'Watered daily, staked on week 3', '5 kg']);
    $insertLog->execute([2, 2, 'Kangkong', 'Harvested twice this month', '10 bundles']);

    // --- Resources + one pending request ---
    $insertResource = $pdo->prepare("INSERT INTO RESOURCE (Name, TotalQty, AvailableQty) VALUES (?, ?, ?)");
    $insertResource->execute(['Shovel', 5, 4]);
    $insertResource->execute(['Wheelbarrow', 2, 2]);
    $insertResource->execute(['Watering Can', 8, 8]);
    $insertResource->execute(['Fertilizer (bag)', 20, 20]);

    $pdo->prepare("INSERT INTO RESOURCE_TXN (GardenerID, ResourceID, Qty, Status) VALUES (?, ?, ?, 'Requested')")
        ->execute([1, 1, 1]); // Maria requesting a shovel

    // --- Exchange listings ---
    $insertListing = $pdo->prepare("INSERT INTO EXCHANGE_LISTING (GardenerID, Crop, Qty, Notes) VALUES (?, ?, ?, ?)");
    $listings = [
        [1, 'Tomatoes', 5, 'Freshly picked this morning, kg basis'],
        [2, 'Kangkong', 10, 'Bundle of 10, happy to trade for herbs'],
        [3, 'Calamansi', 20, 'Small but juicy, pesticide-free'],
        [1, 'Okra', 8, 'Great for sinigang'],
        [2, 'Sili', 15, 'Labuyo, spicy variety'],
    ];
    foreach ($listings as $l) {
        $insertListing->execute($l);
    }
}
