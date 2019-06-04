const $ = window.$
export function showFogLoading() {
    $(document).ready(function () {
        $('#app-loading').css({display: 'flex'});
    });
}

export function hideFogLoading() {
    $(document).ready(function () {
        $('#app-loading').css({display: 'none'});
        $('#app-loading-more').css({display: 'none'});
    });
}

export function showMoreLoading() {
    $(document).ready(function () {
        $('#app-loading-more').css({display: 'flex'});
    });
}

export function hideMoreLoading() {
    $(document).ready(function () {
        $('#app-loading-more').css({display: 'none'});
    });
}