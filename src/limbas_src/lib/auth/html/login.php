<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
require_once(COREPATH . 'lib/db/db_wrapper.lib');
require_once(COREPATH . 'lib/include.lib');
//require_once("lib/session.lib");

$db = Database::get();


# get company name
$sqlquery2 = "SELECT ID,FORM_NAME,NORM FROM LMB_UMGVAR WHERE FORM_NAME = 'company' OR FORM_NAME = 'page_title'";
$rs2 = lmbdb_exec($db,$sqlquery2) or errorhandle(lmbdb_errormsg($db),$sqlquery2,'login',__FILE__,__LINE__);

$umgvar = [];
while(lmbdb_fetch_row($rs2)){
    $umgvar[lmbdb_result($rs2,'FORM_NAME')] = lmbdb_result($rs2,'NORM');
}
$pageTitleText = 'Login';
$pageTitle = $umgvar['page_title'] ? sprintf($umgvar['page_title'], $pageTitleText) : $pageTitleText;

?>


<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?=$pageTitle?></title>

    <link rel="stylesheet" href="assets/css/default.css">
</head>

<body>

<section class="vh-100 bg-secondary">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-2-strong rounded-3">
                    <div class="card-body p-4 p-sm-5 text-center">
                        <form method="post">
                            <h3 class="mb-5">Login</h3>
                            
                            <?php if(isset($wrongCredentials)): ?>

                                <div class="alert alert-danger rounded" role="alert">
                                    Username or password wrong.
                                </div>
                            
                            <?php endif; ?>
    
                            <div class="input-group mb-3">
                                <span class="input-group-text rounded-start"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control form-control-lg rounded-end" id="username" name="username" placeholder="Username">
                            </div>
    
                            <div class="input-group mb-4">
                                <span class="input-group-text rounded-start"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control form-control-lg rounded-end" id="password" name="password" placeholder="Password">
                            </div>
    
                            <div class="d-grid">
                                <button class="btn btn-primary btn-lg btn-block" type="submit">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>
