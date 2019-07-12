export const smoothScrollToView = (querySelector, duration = 350) => {
    if(querySelector && querySelector.offset() instanceof Object){
        const offsetTop = querySelector.offset().top;

        const elementHeight = querySelector.height();
        const windowHeight = $(window).height();
        let offset = offsetTop;

        $('html, body').animate({
            scrollTop: offset
        }, duration);
    }

}