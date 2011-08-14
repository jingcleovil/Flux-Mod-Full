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
    
    $sql  = "SELECT c.char_id,c.online,c.zeny,c.account_id,c.name,c.char_num as slot,c.class as jclass,c.base_level as blevel,c.job_level as jlevel,g.name as gname FROM {$server->loginDatabase}.`char` AS c ";
    $sql .= "JOIN login as l ON l.account_id = c.account_id "; 
    $sql .= "LEFT JOIN guild AS g ON (g.char_id = c.char_id) ";
    $sql .= "WHERE `l`.`account_id` > 1 ";
    $sql .= isset($sqlpartial) ? $sqlpartial : " ";
    $sql .= "ORDER BY c.char_id ASC LIMIT 0,15";
    $sth  = $server->connection->getStatement($sql);
    $sth->execute($bind); 
    
    $json['sql'] = $sql;
    
    
    $chars = $sth->fetchAll();
    $res = array();
    $class = 'odd'; 
    $i = 0;
    
    $json['account'] = count($accounts);
    
    foreach($chars as $char)
    {
        if(!$job=$this->jobClassText($char->jclass)) $job = "Unknown";    
        
        if(!$guild = $char->gname) $guild = "";
        
        if($char->online == 0) $online = "Offline"; else $online = "Online";
        
        $res[] = array(
                    'char_id'    =>  '<a href="'.$this->url('character','view',array('id'=>$char->char_id)).'">'.$char->char_id."</a>",
                    'account_id'    =>  '<a href="'.$this->url('account','view',array('id'=>$char->account_id)).'">'.$char->account_id."</a>",
                    'name'       =>  $char->name,
                    'jclass'     =>  $job,
                    'jlevel'     =>  $char->jlevel,
                    'blevel'     =>  $char->blevel,
                    'zeny'       =>  $char->zeny,
                    'guild'      =>  $guild,
                    'class'      =>  $i&0 == 0 ? 'odd' : 'even',
                    'online'     =>  $online, 
                    'slot'       =>  $char->slot
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