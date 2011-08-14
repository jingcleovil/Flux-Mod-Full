var procJSON = {
    'retry' : function(form,data)
    {                                           
        $('.response',form).html(data.message).slideDown('fast'); 
        
		if(submod == "create")
        {
			
            refreshSecurityCode('.security-code img');
        }   
    },
    'success' : function(form,data)
    {
        ticketForm(form,60,true);
        ticketList(1);                   
    },
    'forward' : function(form,data)
    {
        location.href=data.url;    
    }
}

$(document).ready(function(){

    $('.form').submit(function(e){
        e.preventDefault();
        
        var form = this;
        var action = $(this).attr('action');  
        var dt = $(form).serializeArray();

        
        $('.process',form).html('<div class="preloader">Processing, please wait...</div>').show();
        
        $('table',form).hide();

        $.ajax({
            url : action,
            dataType: 'json',
            type: 'post',
            data: dt,
            success: function(edata)
            {
                try
                {
                    procJSON[edata.action](form,edata);
                    $('.process',form).hide();
                }        
                catch(e)
                {
                    $('.process',form).hide();
                    $('.response',form).html('<div class="error"><h4>Error Encountered:</h4><p>'+e+'</p><a href="" class="close" onclick="return showForm(this,false)">TRY AGAIN</a></div>');
                }
            },
            error: function(xhr)
            {
                alert(xhr.responseText);
                
                $('.response',form).html('<div class="error"><h4>Error Encountered:</h4><p>Please contact administrator.</p><a href="" class="close" onclick="return showForm(this,false)">TRY AGAIN</a></div>');
                
            }    
        }); 
    });
     
});

function showForm(target,reset)
{
    var form = $(target).parents('form');
    $('table',form).show();
    $('.response',form).empty(); 
    
    if(reset)
    {
        $(form).trigger('reset');
    }
    
    return false;   
}
