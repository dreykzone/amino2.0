<?php
require_once __DIR__ . '/db.php';

const MIGRATIONS_DIR = __DIR__ . '/database/migrations';
const MIGRATION_LOCK = 'amino2_migrations_lock';

function failMigration(string $message): never
{
    fwrite(STDERR, "Migration error: {$message}" . PHP_EOL);
    exit(1);
}

$conn->set_charset('utf8mb4');

if (!$conn->query(
    "CREATE TABLE IF NOT EXISTS migrations (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        migration VARCHAR(255) NOT NULL,
        applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_migrations_migration (migration)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
)) {
    failMigration($conn->error);
}

$lockResult = $conn->query("SELECT GET_LOCK('" . MIGRATION_LOCK . "', 10) AS acquired");
if (!$lockResult || (int) $lockResult->fetch_assoc()['acquired'] !== 1) {
    failMigration('could not acquire the migration lock');
}

try {
    $files = glob(MIGRATIONS_DIR . '/*.sql') ?: [];
    usort($files, static fn(string $left, string $right): int => strnatcasecmp(
        basename($left),
        basename($right)
    ));

    $applied = [];
    $result = $conn->query('SELECT migration FROM migrations ORDER BY migration');
    if (!$result) {
        failMigration($conn->error);
    }
    while ($row = $result->fetch_assoc()) {
        $applied[$row['migration']] = true;
    }

    $pending = 0;
    foreach ($files as $file) {
        $migration = basename($file);

        if (isset($applied[$migration])) {
            echo "Migration {$migration}... SKIPPED" . PHP_EOL;
            continue;
        }

        $sql = file_get_contents($file);
        if ($sql === false) {
            failMigration("could not read {$migration}");
        }
        if (trim($sql) === '') {
            failMigration("{$migration} is empty");
        }

        echo "Migration {$migration}... ";
        $conn->begin_transaction();

        try {
            if (!$conn->multi_query($sql)) {
                throw new RuntimeException($conn->error);
            }
            while ($conn->more_results()) {
                if (!$conn->next_result()) {
                    throw new RuntimeException($conn->error);
                }
            }

            $stmt = $conn->prepare(
                'INSERT INTO migrations (migration) VALUES (?)'
            );
            if (!$stmt) {
                throw new RuntimeException($conn->error);
            }
            $stmt->bind_param('s', $migration);
            if (!$stmt->execute()) {
                throw new RuntimeException($stmt->error);
            }

            $conn->commit();
            echo "OK" . PHP_EOL;
            $pending++;
        } catch (Throwable $error) {
            $conn->rollback();
            echo "FAILED" . PHP_EOL;
            failMigration("{$migration}: {$error->getMessage()}");
        }
    }

    if ($pending === 0) {
        echo "No pending migrations." . PHP_EOL;
    }
} finally {
    $conn->query("SELECT RELEASE_LOCK('" . MIGRATION_LOCK . "')");
    $conn->close();
}
