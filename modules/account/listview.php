<?php
if (!defined('FLUX_ROOT')) exit;
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(IS_AJAX)
{
    $search = trim($params->get('item'));
    $searchby = trim($params->get('searchby'));
    
    $bind = array();

    if($search)
    {
            $sqlpartial = "AND ".mysql_escape_string($searchby)." LIKE ? ";  
            $bind = array("%$search%"); 

    }
    
    $sql  = "SELECT login.*,cp_credits.balance FROM {$server->loginDatabase}.login ";
    $sql .= "LEFT JOIN cp_credits ON cp_credits.account_id = login.account_id "; 
    $sql .= "WHERE `login`.`account_id` > 1 ";
    $sql .= isset($sqlpartial) ? $sqlpartial : " ";
    $sql .= "LIMIT 0,15";
    $sth  = $server->connection->getStatement($sql);
    $sth->execute($bind); 
    
    $json['sql'] = $sql;
    
    
    $accounts = $sth->fetchAll();
    $res = array();
    $class = 'odd'; 
    $i = 0;
    
    $json['account'] = count($accounts);
    
    foreach($accounts as $account)
    {
        if (!$account->confirmed && $account->confirm_code)
        {
            $account_state = htmlspecialchars(Flux::message('AccountStatePending'));    
        }
        elseif(($state = $this->accountStateText($account->state)) && !$account->unban_time)
        {
            $account_state = $state;     
        }
        elseif($account->unban_time)                      
        {
            $account_state = htmlspecialchars(sprintf(Flux::message('AccountStateTempBanned'), date(Flux::config('DateTimeFormat'), $account->unban_time)));
        }
        $res[] = array(
                    'account_id'    =>  '<a href="'.$this->url('account','view',array('id'=>$account->account_id)).'">'.$account->account_id."</a>",
                    'username'      =>  $account->userid,
                    'gender'        =>  $this->genderText($account->sex),
                    'level'         =>  $account->level,
                    'account_state' =>  $account_state,
                    'email'         =>  $account->email,
                    'class'         =>  $i&0 == 0 ? 'odd' : 'even', 
                    'balance'       =>  number_format((int)$account->balance),
                    'ip'       =>  $account->last_ip,
                    );
        $i++;
    }       
}
else
{
    $this->redirect($this->url('main'));
}

$json['db'] = $res;

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

echo json_encode($json);
exit();