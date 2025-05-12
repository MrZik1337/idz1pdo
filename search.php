<?php
require_once 'db.php';

$log_db_file = __DIR__ . '/log/search_log.sqlite';

$log_pdo = null;

try {

    $log_pdo = new PDO('sqlite:' . $log_db_file);
    $log_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $log_pdo->exec("CREATE TABLE IF NOT EXISTS log (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        query_type VARCHAR(50),
        parameters TEXT,
        timestamp DATETIME
    )");

} catch (\PDOException $e) {
    $log_pdo = null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $query_type = $_POST['query_type'] ?? '';

    echo "<h1>Результати пошуку</h1>";

    if ($log_pdo) {
        $log_params = [];
        switch ($query_type) {
            case 'wards_by_nurse':
                $log_params['nurse_name'] = $_POST['nurse_name'] ?? null;
                break;
            case 'nurses_by_department':
                $log_params['department_name'] = $_POST['department_name'] ?? null;
                break;
            case 'duties_by_shift':
                $log_params['shift_name'] = $_POST['shift_name'] ?? null;
                break;
            default:
                $log_params['raw_post'] = $_POST;
                break;
        }

        $log_params_json = json_encode($log_params);
        $log_timestamp = date('Y-m-d H:i:s');

        try {
            $log_sql = "INSERT INTO log (query_type, parameters, timestamp) VALUES (:query_type, :parameters, :timestamp)";

            $log_stmt = $log_pdo->prepare($log_sql);

            $log_stmt->bindParam(':query_type', $query_type);
            $log_stmt->bindParam(':parameters', $log_params_json);
            $log_stmt->bindParam(':timestamp', $log_timestamp);

            $log_stmt->execute();
        
        } catch (\PDOException $e) {

            echo "<p style='color: orange;'>Попередження: Помилка при логуванні запиту. " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    try {
        switch ($query_type) {
            case 'wards_by_nurse':
                $nurse_name = $_POST['nurse_name'] ?? '';
                if ($nurse_name) {
                    $sql = "SELECT W.name AS ward_name
                            FROM WARD W
                            JOIN NURSE_WARD NW ON W.ID_Ward = NW.FID_Ward
                            JOIN NURSE N ON NW.FID_Nurse = N.ID_Nurse
                            WHERE N.name = :nurse_name";

                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':nurse_name', $nurse_name);
                    $stmt->execute();
                    $wards = $stmt->fetchAll();

                    if ($wards) {
                        echo "<h3>Палати, де чергує " . htmlspecialchars($nurse_name) . ":</h3>";
                        echo "<ul>"; 
                        foreach ($wards as $ward) {
                            echo "<li>" . htmlspecialchars($ward['ward_name']) . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>Медсестра '" . htmlspecialchars($nurse_name) . "' не знайдена або не чергує в жодній палаті.</p>";
                    }
                } else {
                    echo "<p>Будь ласка, введіть ім'я медсестри.</p>";
                }
                break;

            case 'nurses_by_department':
                $department_name = $_POST['department_name'] ?? '';
                if ($department_name !== '') {
                    $sql = "SELECT name, shift
                            FROM NURSE
                            WHERE department = :department";

                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':department', $department_name, PDO::PARAM_INT);
                    $stmt->execute();

                    $nurses = $stmt->fetchAll();

                    if ($nurses) {
                        echo "<h3>Медсестри відділення " . htmlspecialchars($department_name) . ":</h3>";
                        echo "<table>
                                <thead>
                                    <tr>
                                        <th>Медсестра</th>
                                        <th>Зміна</th>
                                    </tr>
                                </thead>
                                <tbody>";
                        foreach ($nurses as $nurse) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($nurse['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($nurse['shift']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>
                              </table>";
                    } else {
                         echo "<p>У відділенні " . htmlspecialchars($department_name) . " не знайдено медсестер.</p>";
                    }
                } else {
                    echo "<p>Будь ласка, введіть номер відділення.</p>";
                }
                break; 

            case 'duties_by_shift':
                $shift_name = $_POST['shift_name'] ?? '';
                $allowed_shifts = ['First', 'Second', 'Third'];
                if ($shift_name && in_array($shift_name, $allowed_shifts)) {
                    $sql = "SELECT N.name AS nurse_name, W.name AS ward_name
                            FROM NURSE N
                            JOIN NURSE_WARD NW ON N.ID_Nurse = NW.FID_Nurse
                            JOIN WARD W ON NW.FID_Ward = W.ID_Ward
                            WHERE N.shift = :shift_name";

                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':shift_name', $shift_name);
                    $stmt->execute();

                    $duties = $stmt->fetchAll();

                    if ($duties) {
                        echo "<h3>Чергування у зміну '" . htmlspecialchars($shift_name) . "':</h3>";
                        echo "<table border='1'>
                                <thead>
                                    <tr>
                                        <th>Медсестра</th>
                                        <th>Палата</th>
                                    </tr>
                                </thead>
                                <tbody>";
                        foreach ($duties as $duty) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($duty['nurse_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($duty['ward_name']) . "</td>";
                            echo "</tr>"; 
                        }
                        echo "</tbody>
                              </table>";
                    } else {
                        echo "<p>У зміну '" . htmlspecialchars($shift_name) . "' не знайдено чергувань.</p>";
                    }

                } elseif ($shift_name && !in_array($shift_name, $allowed_shifts)) {
                     echo "<p>Некоректна назва зміни. Будь ласка, введіть 'First', 'Second' або 'Third'.</p>";
                }
                 else {
                    echo "<p>Будь ласка, введіть назву зміни.</p>";
                }
                break;

            default:
                echo "<p>Невідомий тип запиту.</p>";
                break;
        }
    } catch (\PDOException $e) {
        echo "<p>Помилка бази даних: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>Цей файл призначений для обробки даних форм.</p>";
}
echo "<p><a href='index.html'>Повернутись до форм</a></p>";
?>