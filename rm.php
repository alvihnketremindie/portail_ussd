<?php

include_once('global.php');
clean(LOG_PATH, 7);
clean(SESSION_FILE_DIR, 2);
clean("/var/log/ussd_menu_bf/", 7);
?>
