<!DOCTYPE html>
<html lang="en">
<head>
    <base href= "/" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>filesGO!</title>
    <link rel="stylesheet" href="./assets/css/assets.css"/>
    <link rel="stylesheet" href="./assets/css/main.css"/>
</head>
<body>
    <?php

$allowed_levels = array(9,8,7,0);
require_once 'bootstrap.php';

$page_title = __('Log in','cftp_admin');

$body_class = array('login');
$page_id = 'login';
global $auth;

if ($_POST) {
    switch ($_POST['do']) {
        default:
            header("Location: ".PAGE_STATUS_CODE_403);
            exit;
        break;
        case 'login':
            $login = $auth->authenticate($_POST['username'], $_POST['password']);
            $decoded = json_decode($login);
            if ($decoded->status == 'success') {
                $user = new \ProjectSend\Classes\Users;
                $user->get($decoded->user_id);

                /** Record the action log */
                $logger = new \ProjectSend\Classes\ActionsLog;
                $new_record_action = $logger->addEntry([
                    'action' => 1,
                    'owner_id' => $user->id,
                    'owner_user' => $user->username,
                    'affected_account_name' => $user->name
                ]);

                header("Location: ".$decoded->location);
                exit;
            } else {
                $login_error = $decoded->type;
            }
            $auth->setLanguage($_POST['language']);
        break;
    }
}

if ( isset($_SESSION['errorstate'] ) ) {
    $errorstate = $_SESSION['errorstate'];
    unset($_SESSION['errorstate']);
}

$csrf_token = getCsrfToken();

include_once ADMIN_VIEWS_DIR . DS . 'header-unlogged.php';
?>
<div class="col-xs-12 col-sm-12 col-lg-4 col-lg-offset-4">

    <?php echo get_branding_layout(true); ?>

    <?php
        $login_types = array(
            'local' => '1',
            'ldap' => get_option('ldap_signin_enabled'),
        );
    ?>
    <div class="white-box">
        <div class="white-box-interior">
            <div class="ajax_response">
                <?php
                    /** Coming from an external form */
                    if ( isset( $login_error ) ) {
                        echo system_message('danger', $auth->getLoginError($login_error));
                    }
                ?>
            </div>
        
            <?php /*
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#local" aria-controls="local" role="tab" data-toggle="tab">Local account</a></li>
                <?php if ($login_types['ldap'] == 'true') { ?>
                    <li role="presentation"><a href="#ldap" aria-controls="ldap" role="tab" data-toggle="tab">LDAP</a></li>
                <?php } ?>
            </ul> */ ?>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="local">
                    <?php include_once FORMS_DIR . DS . 'login.php'; ?>

                    <div class="login_form_links">
                        <p id="reset_pass_link"><?php _e("Forgot your password?",'cftp_admin'); ?> <a href="<?php echo BASE_URI; ?>reset-password.php"><?php _e('Set up a new one.','cftp_admin'); ?></a></p>
                        <?php
                            if (get_option('clients_can_register') == '1') {
                        ?>
                                <p id="register_link"><?php _e("Don't have an account yet?",'cftp_admin'); ?> <a href="<?php echo BASE_URI; ?>register.php"><?php _e('Register as a new client.','cftp_admin'); ?></a></p>
                        <?php
                            } else {
                        ?>
                                <p><?php _e("This server does not allow self registrations.",'cftp_admin'); ?></p>
                                <p><?php _e("If you need an account, please contact a server administrator.",'cftp_admin'); ?></p>
                        <?php
                            }
                        ?>
                    </div>
                </div>

                <?php /* if ($login_types['ldap'] == 'true') { ?>
                    <div role="tabpanel" class="tab-pane fade" id="ldap">
                        <?php include_once FORMS_DIR . DS . 'login-ldap.php'; ?>
                    </div>
                <?php } */ ?>
            </div>
        </div>
    </div>
</div>

<?php
    include_once ADMIN_VIEWS_DIR . DS . 'footer.php';
</body>
</html>