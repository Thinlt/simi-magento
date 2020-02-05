/**
 * popular function in vnecoms
 */
define([
    "jquery",
    "jquery/ui",
], function($){
    function vesReplace(re, str, content) {
        return content.replace(re, str);
    }

    window.vesReplace = vesReplace;
});