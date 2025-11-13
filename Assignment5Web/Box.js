$(document).ready(function() {
    const $dialog = $('#dialog');
    let isDragging = false;
    let isResizing = false;
    let resizeDir = '';
    let startX, startY, startWidth, startHeight, startLeft, startTop;

    $('#open-dialog').click(function() {
        const winWidth = $(window).width();
        const winHeight = $(window).height();
        const dialogWidth = 400;
        const dialogHeight = 300;
        
        $dialog.css({
            left: (winWidth - dialogWidth) / 2 + 'px',
            top: (winHeight - dialogHeight) / 2 + 'px',
            width: dialogWidth + 'px',
            height: dialogHeight + 'px'
        }).show();
    });

    $dialog.find('.dialog-close').on({
        mousedown: function(e) {
            e.stopPropagation(); 
        },
        click: function() {
            $dialog.hide();
            closing_dialog_alert();
        }
    });

    $dialog.find('.dialog-header').mousedown(function(e) {
        if (e.button !== 0) return;
        
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        startLeft = parseInt($dialog.css('left'), 10);
        startTop = parseInt($dialog.css('top'), 10);
    });

    $dialog.find('.resize-handle').mousedown(function(e) {
        if (e.button !== 0) return;
        
        isResizing = true;
        resizeDir = $(this).attr('class').split(' ')[1].split('-')[1];
        startX = e.clientX;
        startY = e.clientY;
        startWidth = $dialog.outerWidth();
        startHeight = $dialog.outerHeight();
        startLeft = parseInt($dialog.css('left'), 10);
        startTop = parseInt($dialog.css('top'), 10);
        e.preventDefault();
    });

    $(document).mousemove(function(e) {
        if (isDragging) {
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;
            
            $dialog.css({
                left: startLeft + deltaX + 'px',
                top: startTop + deltaY + 'px'
            });
        } else if (isResizing) {
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;
            const newPos = calculateNewSize(resizeDir, deltaX, deltaY);
            
            $dialog.css({
                width: Math.max(200, newPos.width) + 'px',
                height: Math.max(150, newPos.height) + 'px',
                left: newPos.left + 'px',
                top: newPos.top + 'px'
            });
        }
    });

    $(document).mouseup(() => {
        isDragging = false;
        isResizing = false;
    });

    function closing_dialog_alert(){ alert("The dialog box will close!"); }

    function calculateNewSize(dir, deltaX, deltaY) {
        const result = {
            width: startWidth,
            height: startHeight,
            left: startLeft,
            top: startTop
        };
    
        switch (dir) {
            case 'e': 
                result.width = startWidth + deltaX;
                break;
            case 'w': 
                if (startWidth - deltaX >= 200) {
                    result.width = startWidth - deltaX;
                    result.left = startLeft + deltaX;
                } else {
                    result.width = 200;
                }
                break;
            case 's': 
                result.height = startHeight + deltaY;
                break;
            case 'n': 
                if (startHeight - deltaY >= 150) {
                    result.height = startHeight - deltaY;
                    result.top = startTop + deltaY;
                } else {
                    result.height = 150;
                }
                break;
            case 'se': 
                result.width = startWidth + deltaX;
                result.height = startHeight + deltaY;
                break;
            case 'sw': 
                if (startWidth - deltaX >= 200) {
                    result.width = startWidth - deltaX;
                    result.left = startLeft + deltaX;
                } else {
                    result.width = 200;
                }
                result.height = startHeight + deltaY;
                break;
            case 'ne': 
                result.width = startWidth + deltaX;
                if (startHeight - deltaY >= 150) {
                    result.height = startHeight - deltaY;
                    result.top = startTop + deltaY;
                } else {
                    result.height = 150;
                }
                break;
            case 'nw': 
                if (startWidth - deltaX >= 200) {
                    result.width = startWidth - deltaX;
                    result.left = startLeft + deltaX;
                } else {
                    result.width = 200;
                }
                if (startHeight - deltaY >= 150) {
                    result.height = startHeight - deltaY;
                    result.top = startTop + deltaY;
                } else {
                    result.height = 150;
                }
                break;
        }
    
        result.width = Math.max(200, result.width);
        result.height = Math.max(150, result.height);
    
        return result;
    }
});