jQuery(document).ready(function($){
    var down = false;
    $(document).on('mousedown', '#handle', function(e){
        down = true;
        e.preventDefault(); // Prevent triggering default drag on element
    }).on('mousemove', function(e){

        var $div = $('#sidebar'),
            $content = $('#content'),
            $handle = $('#handle'),
            handleX = $handle.offset().left, // x from document
            cursorX = e.pageX, // x from document
            divW = $div.width(),
            diff = 0;

        if(down){

            if( handleX > cursorX ){
                diff = handleX - cursorX;
                if( diff > 5){
                    $div.width(divW-diff);
                    $content.css('margin-left', $div.width());
                }
            } else if(cursorX > handleX){
                diff = cursorX - handleX;
                if( diff > 5){
                    $div.width(divW+diff);
                    $content.css('margin-left', $div.width());
                }
            }
        }
    }).on('mouseup', function(e){
        down = false;
    }).on('click', '#sidebar-x', function(){
        $('body').toggleClass('hide');
    }).on('click', '#sidebar-show', function(){
        $('body').toggleClass('hide');
    });
});