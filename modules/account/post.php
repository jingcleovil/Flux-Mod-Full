<?php
if (!defined('FLUX_ROOT')) exit;

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(IS_AJAX)
{
    error_reporting(E_ALL);  
    $action = trim($params->get('act'));
    
    switch($action)
    {
        case 'create':
                if (Flux::config('UseCaptcha') && Flux::config('EnableReCaptcha')) {
                    require_once 'recaptcha/recaptchalib.php';
                    $recaptcha = recaptcha_get_html(Flux::config('ReCaptchaPublicKey'));
                }

                $serverNames = $this->getServerNames();

                if (count($_POST)) {
                    require_once 'Flux/RegisterError.php';
                    
                    try {
                        $server   = $params->get('server');
                        $username = $params->get('username');
                        $password = $params->get('password');
                        $confirm  = $params->get('confirm_password');
                        $email    = $params->get('email_address');
                        $gender   = $params->get('gender');
                        $code     = $params->get('security_code');
                        
                        if (!($server = Flux::getServerGroupByName($server))) {
                            throw new Flux_RegisterError('Invalid server', Flux_RegisterError::INVALID_SERVER);
                        }
                        
                        // Woohoo! Register ;)
                        $result = $server->loginServer->register($username, $password, $confirm, $email, $gender, $code);

                        if ($result) {
                            if (Flux::config('RequireEmailConfirm')) {
                                require_once 'Flux/Mailer.php';
                                
                                $user = $username;
                                $code = md5(rand());
                                $name = $session->loginAthenaGroup->serverName;
                                $link = $this->url('account', 'confirm', array('_host' => true, 'code' => $code, 'user' => $username, 'login' => $name));
                                $mail = new Flux_Mailer();
                                $sent = $mail->send($email, 'Account Confirmation', 'confirm', array('AccountUsername' => $username, 'ConfirmationLink' => htmlspecialchars($link)));
                                
                                $createTable = Flux::config('FluxTables.AccountCreateTable');
                                $bind = array($code);
                                
                                // Insert confirmation code.
                                $sql  = "UPDATE {$server->loginDatabase}.{$createTable} SET ";
                                $sql .= "confirm_code = ?, confirmed = 0 ";
                                if ($expire=Flux::config('EmailConfirmExpire')) {
                                    $sql .= ", confirm_expire = ? ";
                                    $bind[] = date('Y-m-d H:i:s', time() + (60 * 60 * $expire));
                                }
                                
                                $sql .= " WHERE account_id = ?";
                                $bind[] = $result;
                                
                                $sth  = $server->connection->getStatement($sql);
                                $sth->execute($bind);
                                
                                $session->loginServer->permanentlyBan(null, sprintf(Flux::message('AccountConfirmBan'), $code), $result);
                                
                                if ($sent) {
                                    $message  = Flux::message('AccountCreateEmailSent');
                                }
                                else {
                                    $message  = Flux::message('AccountCreateFailed');
                                }
                                
                                $session->setMessageData($message);
                                $json['action'] = "forward";
                                $json['url'] = $this->url('account','view');
                            }
                            else {
                                $session->login($server->serverName, $username, $password, false);
                                $session->setMessageData(Flux::message('AccountCreated'));
                                $json['action'] = "forward";
                                $json['url'] = $this->url('account','view');
                            }
                            $json['action'] = "forward";
                            $json['url'] = $this->url('account','view');
                        }
                        else {
                            //exit('Uh oh, what happened?'); 
                            $json['message'] = 'Uh oh, what happened?'; 
                            $json['message'] = "<div class=\"error\">";
                            $json['message'] .= "<h4>".htmlentities(Flux::message('ajaxErrorHeader'))."</h4>";
                            $json['message'] .= "<p>Uh oh, what happened?</p>";
                            
                            $json['message'] .= "<a href=\"\" class=\"close\" onclick=\"return showForm(this,false)\">TRY AGAIN</a>"; 
                            $json['message'] .= "</div>";
                            $json['action'] = "retry";
                            
                            header('Cache-Control: no-cache, must-revalidate');
                            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                            header('Content-type: application/json');

                            echo json_encode($json);
                            exit();
                            
                        }
                    }
                    catch (Flux_RegisterError $e) {
                        switch ($e->getCode()) {
                            case Flux_RegisterError::USERNAME_ALREADY_TAKEN:
                                $errorMessage = Flux::message('UsernameAlreadyTaken');
                                break;
                            case Flux_RegisterError::USERNAME_TOO_SHORT:
                                $errorMessage = Flux::message('UsernameTooShort');
                                break;
                            case Flux_RegisterError::USERNAME_TOO_LONG:
                                $errorMessage = Flux::message('UsernameTooLong');
                                break;
                            case Flux_RegisterError::PASSWORD_TOO_SHORT:
                                $errorMessage = Flux::message('PasswordTooShort');
                                break;
                            case Flux_RegisterError::PASSWORD_TOO_LONG:
                                $errorMessage = Flux::message('PasswordTooLong');
                                break;
                            case Flux_RegisterError::PASSWORD_MISMATCH:
                                $errorMessage = Flux::message('PasswordsDoNotMatch');
                                break;
                            case Flux_RegisterError::EMAIL_ADDRESS_IN_USE:
                                $errorMessage = Flux::message('EmailAddressInUse');
                                break;
                            case Flux_RegisterError::INVALID_EMAIL_ADDRESS:
                                $errorMessage = Flux::message('InvalidEmailAddress');
                                break;
                            case Flux_RegisterError::INVALID_GENDER:
                                $errorMessage = Flux::message('InvalidGender');
                                break;
                            case Flux_RegisterError::INVALID_SERVER:
                                $errorMessage = Flux::message('InvalidServer');
                                break;
                            case Flux_RegisterError::INVALID_SECURITY_CODE:
                                $errorMessage = Flux::message('InvalidSecurityCode');
                                break;
                            default:
                                $errorMessage = Flux::message('CriticalRegisterError');
                                break;
                        }
                        $json['message'] = "<div class=\"error\">";
                        $json['message'] .= "<h4>".htmlentities(Flux::message('ajaxErrorHeader'))."</h4>";
                        $json['message'] .= "<p>$errorMessage</p>";
                        
                        $json['message'] .= "<a href=\"\" class=\"close\" onclick=\"return showForm(this,false)\">TRY AGAIN</a>"; 
                        $json['message'] .= "</div>";
                        $json['action'] = "retry";
                    }
                }
            break;
        case 'login':
            if (count($_POST)) {
                $server   = $params->get('server');
                $username = $params->get('username');
                $password = $params->get('password');
                $code     = $params->get('security_code');
                
                try {
                    $session->login($server, $username, $password, $code);
                    $returnURL = $params->get('return_url');
                    
                    if ($session->loginAthenaGroup->loginServer->config->getUseMD5()) {
                        $password = Flux::hashPassword($password);
                    }
                    
                    $sql  = "INSERT INTO {$session->loginAthenaGroup->loginDatabase}.$loginLogTable ";
                    $sql .= "(account_id, username, password, ip, error_code, login_date) ";
                    $sql .= "VALUES (?, ?, ?, ?, ?, NOW())";
                    $sth  = $session->loginAthenaGroup->connection->getStatement($sql);
                    $sth->execute(array($session->account->account_id, $username, $password, $_SERVER['REMOTE_ADDR'], null));
                    
                    if ($returnURL) {
                        $url = $this->redirect($returnURL);
                    }
                    else {
                        //$this->redirect();
                        $url = $this->url('account','view');  
                    }
                    $json['url'] = $url;
                    $json['action'] = "forward";
                }
                catch (Flux_LoginError $e) {
                    if ($username && $password && $e->getCode() != Flux_LoginError::INVALID_SERVER) {
                        $loginAthenaGroup = Flux::getServerGroupByName($server);

                        $sql = "SELECT account_id FROM {$loginAthenaGroup->loginDatabase}.login WHERE ";
                        
                        if (!$loginAthenaGroup->loginServer->config->getNoCase()) {
                            $sql .= "CAST(userid AS BINARY) ";
                        } else {
                            $sql .= "userid ";
                        }
                        
                        $sql .= "= ? LIMIT 1";
                        $sth = $loginAthenaGroup->connection->getStatement($sql);
                        $sth->execute(array($username));
                        $row = $sth->fetch();

                        if ($row) {
                            $accountID = $row->account_id;
                            
                            if ($loginAthenaGroup->loginServer->config->getUseMD5()) {
                                $password = Flux::hashPassword($password);
                            }

                            $sql  = "INSERT INTO {$loginAthenaGroup->loginDatabase}.$loginLogTable ";
                            $sql .= "(account_id, username, password, ip, error_code, login_date) ";
                            $sql .= "VALUES (?, ?, ?, ?, ?, NOW())";
                            $sth  = $loginAthenaGroup->connection->getStatement($sql);
                            $sth->execute(array($accountID, $username, $password, $_SERVER['REMOTE_ADDR'], $e->getCode()));
                        }
                    }
                    
                    switch ($e->getCode()) {
                        case Flux_LoginError::UNEXPECTED:
                            $errorMessage = Flux::message('UnexpectedLoginError');
                            break;
                        case Flux_LoginError::INVALID_SERVER:
                            $errorMessage = Flux::message('InvalidLoginServer');
                            break;
                        case Flux_LoginError::INVALID_LOGIN:
                            $errorMessage = Flux::message('InvalidLoginCredentials');
                            break;
                        case Flux_LoginError::BANNED:
                            $errorMessage = Flux::message('TemporarilyBanned');
                            break;
                        case Flux_LoginError::PERMABANNED:
                            $errorMessage = Flux::message('PermanentlyBanned');
                            break;
                        case Flux_LoginError::IPBANNED:
                            $errorMessage = Flux::message('IpBanned');
                            break;
                        case Flux_LoginError::INVALID_SECURITY_CODE:
                            $errorMessage = Flux::message('InvalidSecurityCode');
                            break;
                        case Flux_LoginError::PENDING_CONFIRMATION:
                            $errorMessage = Flux::message('PendingConfirmation');
                            break;
                        default:
                            $errorMessage = Flux::message('CriticalLoginError');
                            break;
                    }
                    $json['message'] = "<div class=\"error\">";
                    $json['message'] .= "<h4>Please correct the following error:</h4>";
                    $json['message'] .= "<p>$errorMessage</p>";
                    
                    $json['message'] .= "<a href=\"\" class=\"close\" onclick=\"return showForm(this,false)\">TRY AGAIN</a>"; 
                    $json['message'] .= "</div>";
                    $json['action'] = "retry";
                }
            }
            break;
        case 'changepassword':
            if (count($_POST)) {
                $currentPassword    = $params->get('currentpass');
                $newPassword        = trim($params->get('newpass'));
                $confirmNewPassword = trim($params->get('confirmnewpass'));
                
                if (!$currentPassword) {
                    $errorMessage = Flux::message('NeedCurrentPassword');
                }
                elseif (!$newPassword) {
                    $errorMessage = Flux::message('NeedNewPassword');
                }
                elseif (strlen($newPassword) < Flux::config('MinPasswordLength')) {
                    $errorMessage = Flux::message('PasswordTooShort');
                }
                elseif (strlen($newPassword) > Flux::config('MaxPasswordLength')) {
                    $errorMessage = Flux::message('PasswordTooLong');
                }
                elseif (!$confirmNewPassword) {
                    $errorMessage = Flux::message('ConfirmNewPassword');
                }
                elseif ($newPassword != $confirmNewPassword) {
                    $errorMessage = Flux::message('PasswordsDoNotMatch');
                }
                elseif ($newPassword == $currentPassword) {
                    $errorMessage = Flux::message('NewPasswordSameAsOld');
                }
                else {
                    $sql = "SELECT user_pass AS currentPassword FROM {$server->loginDatabase}.login WHERE account_id = ?";
                    $sth = $server->connection->getStatement($sql);
                    $sth->execute(array($session->account->account_id));
                    
                    $account         = $sth->fetch();
                    $useMD5          = $session->loginServer->config->getUseMD5();
                    $currentPassword = $useMD5 ? Flux::hashPassword($currentPassword) : $currentPassword;
                    $newPassword     = $useMD5 ? Flux::hashPassword($newPassword) : $newPassword;
                    
                    if ($currentPassword != $account->currentPassword) {
                        $errorMessage = Flux::message('OldPasswordInvalid');
                    }
                    else {
                        if($currentPassword != "admin" || $currentPassword != "player")
                        {
                            $sql = "UPDATE {$server->loginDatabase}.login SET user_pass = ? WHERE account_id = ?";
                            $sth = $server->connection->getStatement($sql);
                            
                            if ($sth->execute(array($newPassword, $session->account->account_id))) {
                                $pwChangeTable = Flux::config('FluxTables.ChangePasswordTable');
                                
                                $sql  = "INSERT INTO {$server->loginDatabase}.$pwChangeTable ";
                                $sql .= "(account_id, old_password, new_password, change_ip, change_date) ";
                                $sql .= "VALUES (?, ?, ?, ?, NOW())";
                                $sth  = $server->connection->getStatement($sql);
                                $sth->execute(array($session->account->account_id, $currentPassword, $newPassword, $_SERVER['REMOTE_ADDR']));
                                
                                $session->setMessageData(Flux::message('PasswordHasBeenChanged'));
                                $session->logout();
                                //$this->redirect($this->url('account', 'login'));
                                $url = $this->url('account', 'login');
                            }
                            else {
                                $errorMessage = Flux::message('FailedToChangePassword');
                            }
                        }
                        else
                        {
                            $errorMessage = "It works. But demo page can't change password";
                        }
                    }
                }
                if(isset($errorMessage))
                {
                    $json['message'] = "<div class=\"error\">";
                    $json['message'] .= "<h4>".htmlentities(Flux::message('ajaxErrorHeader'))."</h4>";
                    $json['message'] .= "<p>$errorMessage</p>";
                    
                    $json['message'] .= "<a href=\"\" class=\"close\" onclick=\"return showForm(this,false)\">TRY AGAIN</a>"; 
                    $json['message'] .= "</div>";
                    $json['action'] = "retry";
                }
                else
                {             
                    $json['action'] = "forward";
                    $json['url'] = $url;    
                }
            }
            break;
        default: 
            break;
    }
}
else
{
    $this->redirect($this->url('main'));
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

echo json_encode($json);
exit();
?>