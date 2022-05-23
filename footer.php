                </div> <!-- container-fluid -->

                <?php
                
                    default_footer_info();
                    
                    render_json_variables();
                    
                    load_js_files();
                ?>
            </div> <!-- main_content -->
        </div> <!-- container-custom -->
    </body>
</html>
<?php
    if ( DEBUG === true ) {
        echo "\n" . '<!-- DEBUG INFORMATION' . "\n";
        // Print the total count of queries made by PDO
        _e('Executed queries','cftp_admin'); echo ': ' . $dbh->GetCount();
        echo "\n" . '-->' . "\n" ;
    }

    ob_end_flush();?>