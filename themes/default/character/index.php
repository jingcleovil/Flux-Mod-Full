<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Characters</h2>

<div class="search-frm">
    <div class="trow">
        <label>SEARCH ACCOUNT</label>
        <input type="text" name="item" class="search"/>
    </div>
    <div class="trow">
        <label>SEARCH BY</label>
        <select name="searchby">
            <option value="c.name">Char Name</option>
            <option value="c.char_id">Char ID</option>
            <option value="l.account_id">Account ID</option>
        </select>
    </div>
</div>

<div id="tabs">
        <div class="tabs">
            <a href="#">List of Characters</a>
        </div>
        
        <div class="panes">
            <div class="accounts">
                     <table class="mlist">
                        <thead>
                            <tr>
                                <th>Character ID</th>
                                <th>Account</th>
                                <th>Character</th>
                                <th>Job Class</th>
                                <th>Base Level</th>
                                <th>Job Level</th>
                                <th>Zeny</th>
                                <th>Guild</th>
                                <th>Online</th>
                                <th>Slot</th>
                            </tr>
                        </thead>
                        <tbody id="account">
       
                        </tbody>
                    </table>
            </div>
        </div>
         

   </div>
