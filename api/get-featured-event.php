<?php

include "db.php";

$sql = "
SELECT *
FROM events
WHERE event_date >= CURDATE()
ORDER BY event_date ASC
LIMIT 1
";

$result =
mysqli_query($conn,$sql);

echo json_encode(
    mysqli_fetch_assoc($result)
);