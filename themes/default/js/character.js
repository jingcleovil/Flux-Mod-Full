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
                html +=     "<td>"+n.char_id+"</td>"    
                html +=     "<td>"+n.account_id+"</td>" 
                html +=     "<td>"+n.name+"</td>"    
                html +=     "<td>"+n.jclass+"</td>"       
                      
                html +=     "<td>"+n.blevel+"</td>"       
                html +=     "<td>"+n.jlevel+"</td>"       
                html +=     "<td>"+n.zeny+"</td>"       
                html +=     "<td>"+n.guild+"</td>"       
                html +=     "<td>"+n.online+"</td>"       
                html +=     "<td>"+n.slot+"</td>"       
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