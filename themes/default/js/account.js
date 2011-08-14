$('#tabs').tabs();

$().ready(function(){
    $('.search').keypress(function(e){
        if(e.which == 13)
        {
            var item = $(this).val();
            var searchby = $('select[name=searchby]').val();
                        
            if(item.length)
                listView(item,searchby);
            else
                return false;
        }
    })         
});

if(submod == "index") listView('','');

function listView(item,searchby)
{
    
    $('.panes div').addClass('preloader');

    var dt  = { item : item, searchby : searchby } 

    $.ajax({
        url : root + 'listview',
        dataType: 'json',
        data: dt,
        type: 'post',
        success: function(data)
        {
            var edata = data;
            var html = "";
            $('#account').html('');  
            $.each(edata.db,function(i,n){
                html  = "<tr class='"+n.class+" listView'>";
                html +=     "<td>"+n.account_id+"</td>"    
                html +=     "<td>"+n.username+"</td>"    
                html +=     "<td>"+n.gender+"</td>"    
                html +=     "<td>"+n.level+"</td>"    
                html +=     "<td>"+n.account_state+"</td>"    
                html +=     "<td>"+n.balance+"</td>"    
                html +=     "<td>"+n.email+"</td>"    
                html +=     "<td>"+n.ip+"</td>"    
                html += "</tr>"; 
                
                $('#account').append(html);   
            }) 
            $('.panes div').removeClass('preloader');    
        }
        ,error : function(data)
        {
            $('.panes div').html("<p>Can't load ladder. Please contact administrator.</p>");
            $('.panes div').removeClass('preloader'); 
        }
    })
}