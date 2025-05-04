<?php
   session_start();

echo json_encode(['Noti' => $_SESSION['notification'],
                  'Pending' => $_SESSION['pendingLeave']]);
?>