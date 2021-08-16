<?php

switch (WEBFOTO_SCRIPT) {
    case 'CRONJOB':
        // Types

        require_once 'src/types/DriverType.php';
        require_once 'src/types/InputImage.php';
        require_once 'src/types/Image.php';

        // Utils

        require_once 'src/utils/Config.php';
        require_once 'src/utils/DatabaseService.php';
        require_once 'src/utils/ImagesHandler.php';
        require_once 'src/utils/drivers/BaseDriver.php';
        require_once 'src/utils/drivers/DahuaDriver.php';

        break;
    case 'API':
        // Types

        require_once 'src/types/Image.php';

        // Utils

        require_once 'src/utils/Config.php';
        require_once 'src/utils/DatabaseService.php';

        break;
}
