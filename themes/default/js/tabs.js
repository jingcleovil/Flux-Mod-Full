(function( $ ){
    $.fn.tabs = function(options)
    {
        var options = $.extend({
            tab : 'tabs',  // Tabs
            pane : 'panes', // Panes
            selected : 'selected', // Selected Clas    
            index: 0, //default index 
        });
        return this.each(function(){
            var tab = $('.'+options.tab);
            var pane = $('.'+options.pane);
            var sel = options.selected;
            
            $(pane).find('div').eq(options.index).addClass(sel);
            $(tab).find('a').eq(options.index).addClass(sel);
                     
            $('> a',tab).live('click',function(e){
                e.preventDefault();
                var idx = $('> a',tab).index(this);
                
                $('> div',pane).removeClass(sel);
                $('> a',tab).removeClass(sel);
                $(this).addClass(sel);
                $(pane).find('div').eq(idx).addClass(sel);
            });
            

        });
    }    
})( jQuery );