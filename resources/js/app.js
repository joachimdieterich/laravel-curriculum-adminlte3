
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

//vue
window.Vue = require('vue');

// use trans function like in blade
import _ from 'lodash'; //needed to get

Vue.prototype.trans = (key) => {
    return _.get(window.trans, key, key);
};

import VModal from 'vue-js-modal';
Vue.use(VModal, { dynamic: true});

import Sticky from 'vue-sticky-directive';
Vue.use(Sticky);

var filter = function(text, length, clamp){
    clamp = clamp || '...';
    var node = document.createElement('div');
    node.innerHTML = text;
    var content = node.textContent;
    return content.length > length ? content.slice(0, length) + clamp : content;
};

Vue.filter('truncate', filter);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
Vue.component('organization-modal', require('./components/organization/OrganizationModal.vue').default);
Vue.component('group-modal', require('./components/group/GroupModal.vue').default);


Vue.component('absence-modal', require('./components/absence/AbsenceModal.vue').default);
Vue.component('curriculum-view', require('./components/curriculum/CurriculumView.vue').default);
Vue.component('group-view', require('./components/group/GroupView.vue').default);
Vue.component('terminal-objective-modal', require('./components/objectives/TerminalObjectiveModal.vue').default);
Vue.component('data-table-widgets', require('./components/uiElements/DataTableWidgets.vue').default);
Vue.component('enabling-objective-modal', require('./components/objectives/EnablingObjectiveModal.vue').default);
Vue.component('objective-view', require('./components/objectives/ObjectiveView.vue').default);
Vue.component('objective-box', require('./components/objectives/ObjectiveBox.vue').default);
Vue.component('dropdown-button', require('./components/uiElements/DropdownButton.vue').default);
Vue.component('content-modal', require('./components/content/ContentModal.vue').default);
Vue.component('content-create-modal', require('./components/content/ContentCreateModal.vue').default);
Vue.component('content-subscription-modal', require('./components/content/ContentSubscriptionModal.vue').default);
Vue.component('reference-objective-modal', require('./components/reference/ReferenceObjectiveModal.vue').default);
Vue.component('medium-modal', require('./components/media/MediumModal.vue').default);
Vue.component('medium-create-modal', require('./components/media/MediumCreateModal.vue').default);
Vue.component('objective-medium-modal', require('./components/objectives/ObjectiveMediumModal.vue').default);
Vue.component('certificate-generate-modal', require('./components/certificate/GenerateCertificateModal.vue').default);
Vue.component('logbook', require('./components/logbooks/Logbook.vue').default);
Vue.component('logbook-entry-modal', require('./components/logbooks/LogbookEntryModal.vue').default);
Vue.component('subscribe-objective-modal', require('./components/objectives/SubscribeObjectiveModal.vue').default);
Vue.component('task-modal', require('./components/tasks/TaskModal.vue').default);
Vue.component('task', require('./components/tasks/Task.vue').default);
Vue.component('task-timeline', require('./components/tasks/Timeline.vue').default);
Vue.component('kanban-board', require('./components/kanban/KanbanBoard.vue').default);
Vue.component('subscribe-modal', require('./components/subscription/SubscribeModal.vue').default);
Vue.component('sidebar', require('./components/uiElements/Sidebar.vue').default);
Vue.component('move-terminal-objective-modal', require('./components/objectives/MoveTerminalObjectiveModal.vue').default);

Vue.prototype.$initTinyMCE = function (options) {
    tinyMCE.remove();
    tinymce.init({
        path_absolute : "/",
        selector: "textarea.my-editor",
        branding:false,
        plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table directionality",
            "emoticons template paste textpattern example"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media example",
        relative_urls: false,
        entity_encoding : "raw",

        file_browser_callback : function(field_name, url, type, win) {
            var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

            /*var cmsURL =  "/" + 'laravel-filemanager?field_name=' + field_name;
                cmsURL = cmsURL + "&type=Files";

            tinyMCE.activeEditor.windowManager.open({
                file : cmsURL,
                title : 'Filemanager',
                width : x * 0.8,
                height : y * 0.8,
                resizable : "yes",
                close_previous : "no"
            });*/
        }
    });


    tinymce.PluginManager.add('example', function(editor, url) {
        var openDialog = function () {
            document.querySelector("#app").__vue__.$modal.show('medium-create-modal');
            $('#medium_id').on('change', function() {
                //reload thumbs
                editor.setContent('<img src="/media/'+ document.getElementById('medium_id').value +'" width="500">', {format: 'raw'});
            });
        };

        // Add a button that opens a window
        editor.ui.registry.addButton('example', {
            text: 'Medien',
            onAction: function ()  {
                // Open window
                openDialog();
            }
        });

        return {
            getMetadata: function () {
                return  {
                    name: 'Example plugin',
                    url: 'http://exampleplugindocsurl.com'
                };
            }
        };
    });

};
/**
 * Custom Vue directive "can" to check against permissions.
 * If permission is not given element gets style display:none
 *
 * ! Always check permissions in the backand.
 * This directive enables shorter syntax on vue.
 *
 * Example:
 * <element v-can="'curriculum_edit'" ><element>
 *
 * @type Vue
 */

Vue.directive('can', function (el, binding) {
    if(window.Laravel.permissions.indexOf(binding.value) == -1){
        el.style.display = 'none';
    }
    return window.Laravel.permissions.indexOf(binding.value) !== -1;
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
var app = new Vue({
    el: '#app'
});

