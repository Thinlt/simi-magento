require(["prototype", "mage/adminhtml/variables"], function(){

    window.MagentovariablePlugin.loadChooser = function(textareaId) {
        MagentovariablePlugin.textareaId = textareaId;		//set textareId
        Variables.init(null, 'MagentovariablePlugin.insertVariable');
        MagentovariablePlugin.openChooser();

        return;
    }
});