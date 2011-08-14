<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Accounts</h2>

<div class="search-frm">
    <div class="trow">
        <label>SEARCH ACCOUNT</label>
        <input type="text" name="item" class="search"/>
    </div>
    <div class="trow">
        <label>SEARCH BY</label>
        <select name="searchby">
            <option value="userid">Username</option>
            <option value="email">E-mail Address</option>
            <option value="last_ip">Ip Address</option>
        </select>
    </div>
</div>

<div id="tabs">
        <div class="tabs">
            <a href="#">List of Accounts</a>
        </div>
        
        <div class="panes">
            <div class="accounts">
                     <table class="mlist">
                        <thead>
                            <tr>
                                <th>Account ID</th>
                                <th>Username</th>
                                <th>Gender</th>
                                <th>Account Level</th>
                                <th>Account State</th>
                                <th>Credit</th>
                                <th>Email</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody id="account">
       
                        </tbody>
                    </table>
            </div>
        </div>
         

   </div>