
(function () {
    'use strict';

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var register = function (editor) {
        var onAction = function () {
            document.querySelector("#app").__vue__.$modal.show('medium-create-modal', attr);

            if (!document.querySelector("#app").__vue__.$eventHub._events.insertContent){
                document.querySelector("#app").__vue__.$eventHub.on('insertContent', (event) => {
                    console.log(event);
                    if (attr.eventHubCallbackFunctionParams == event.id) {
                        return editor.insertContent('<img src="/media/'+ event.selectedMediumId +'?preview=true" width="500">', {format: 'raw'});
                    }
                    document.querySelector("#app").__vue__.$eventHub._events.insertContent = undefined; //destroy listener to prevent multiple inserts on 2nd, 3rd.. time
                });
            }
        };
        editor.ui.registry.addButton('curriculummedia', {
            text: 'Medien',
            icon: 'image',
            tooltip: 'Medien',
            onAction: onAction
        });

    };

    function Plugin () {
        global.add('curriculummedia', function (editor) {
            register(editor);
        });
    }

    Plugin();

}());
