export const smoothScrollToView = (querySelector, duration = 350, overTheTop=0) => {
    if(querySelector && querySelector.offset() instanceof Object){
        try {
            const offsetTop = querySelector.offset().top - overTheTop;
            const offset = offsetTop;
            $('html, body').animate({
                scrollTop: offset
            }, duration);
        } catch (err) {}
    }
}

