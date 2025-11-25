<!DOCTYPE html>
<html lang="<?=LANG?>">
    <head>
        <title>LIMBAS Installation</title>
        <link rel="stylesheet" href="../assets/css/default.css">
        <script type="text/javascript" src="../assets/vendor/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="../assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="../assets/js/install.js"></script>
    </head>
    
    <body>
        <div class="container py-3">
            <div class="text-center position-relative mb-3">
                <img src="../assets/images/logo.svg" alt="LIMBAS Business Solutions" style="max-height: 8rem">
                <div class="position-absolute top-0 end-0">
                    <select id="lang-select" class="form-select">
                        <option value="en" <?= (LANG == 'en' ? 'selected' : '') ?>>English</option>
                        <option value="de" <?= (LANG == 'de' ? 'selected' : '') ?>>Deutsch</option>
                        <option value="fr" <?= (LANG == 'fr' ? 'selected' : '') ?>>Français</option>
                    </select>
                </div>
            </div>
            <?php require($contentFile); ?>
        </div>
    </body>
</html>
