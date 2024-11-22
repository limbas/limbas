<div class="row justify-content-center">
    <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-body scrollcontainer">

                <?php
                use Limbas\admin\install\Installer;
                /** @var Installer $installer */

                $installer->seedDatabase($language, $dateFormat, $company, $username, $password, $package);
                
                $demoFilesInstalled = $installer->extractDemoFiles($package);
                
                ?>
                    
             <?php if(!$demoFilesInstalled): ?>

                 <div class="alert alert-success mt-3">
                     <?=lang('If you want extension files for demonstration purposes, you can extract the files')?><br>
                     public/localassets/demo.tar.gz<br>
                     dependent/EXTENSIONS/demo.tar.gz
                 </div>
            <?php endif; ?>   
                    
                    
                </div>
            </div>
    </div>
</div>
                    
