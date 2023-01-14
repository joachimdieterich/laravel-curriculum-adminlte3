
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('tinymce/tinymce');

require('@activix/bootstrap-datetimepicker');

//vue
window.Vue = require('vue').default;

window.moment = require('moment');

// use trans function like in blade
import _ from 'lodash'; //needed to get

Vue.prototype.trans = (key) => {
    return _.get(window.trans, key, _.get(window.trans, 'global.' + key.split(".").splice(-1), key) );
};

import "vue-swatches/dist/vue-swatches.css";

/**
 * Store current ab in browser storage
 * example @click="setLocalStorage('#logbook_'+entry.id, '#logbook_description_'+entry.id)"
 * @param key
 * @param value
 */
Vue.prototype.setLocalStorage = (key, value) => {
    localStorage.setItem(key, value);
};
/**
 * check if current value is set in browser storage
 * example: :class="checkLocalStorage('#logbook_'+entry.id, '#logbook_description_'+entry.id)"
 * @param key
 * @param value
 * @param class_string
 * @param is_default_element
 * @returns {string}
 */
Vue.prototype.checkLocalStorage = (key, value, class_string = "active", is_default_element = false) => {
    if (localStorage.getItem(key) === value) {
        return class_string;
    }
    if (localStorage.getItem(key) === null && is_default_element === true) {
        return class_string;
    }
};
/**
 * make userId accessible for vue
 * @type {string}
 */
if (document.querySelector("meta[name='user-id']")){
    Vue.prototype.$userId = document.querySelector("meta[name='user-id']").getAttribute('content');
}



import VModal from 'vue-js-modal';

Vue.use(VModal, {dynamic: true});

import DataTable from 'laravel-vue-datatable';
Vue.use(DataTable);

import Sticky from 'vue-sticky-directive';

Vue.use(Sticky);

import Toast from 'vue-toastification';

import 'vue-toastification/dist/index.css';

Vue.use(Toast, {
    transition: "Vue-Toastification__bounce",
    maxToasts: 20,
    newestOnTop: true
});

var filter = function (text, length, clamp) {
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

Vue.component('admin-view', require('./components/admin/AdminView.vue').default);
Vue.component('absence-modal', require('./components/absence/AbsenceModal.vue').default);
Vue.component('curriculum-view', require('./components/curriculum/CurriculumView.vue').default);
Vue.component('group-view', require('./components/group/GroupView.vue').default);
Vue.component('terminal-objective-modal', require('./components/objectives/TerminalObjectiveModal.vue').default);
Vue.component('data-table-widgets', require('./components/uiElements/DataTableWidgets.vue').default);
Vue.component('enabling-objective-modal', require('./components/objectives/EnablingObjectiveModal.vue').default);
Vue.component('events', require('../../app/Plugins/Eventmanagement/eVewa/resources/js/components/embedEvents').default);
Vue.component('objective-view', require('./components/objectives/ObjectiveView.vue').default);
Vue.component('objective-box', require('./components/objectives/ObjectiveBox.vue').default);
Vue.component('dropdown-button', require('./components/uiElements/DropdownButton.vue').default);
Vue.component('model-limiter', require('./components/config/ModelLimiter.vue').default);
Vue.component('content-modal', require('./components/content/ContentModal.vue').default);
Vue.component('content-create-modal', require('./components/content/ContentCreateModal.vue').default);
Vue.component('content-subscription-modal', require('./components/content/ContentSubscriptionModal.vue').default);
Vue.component('reference-objective-modal', require('./components/reference/ReferenceObjectiveModal.vue').default);
Vue.component('medium-modal', require('./components/media/MediumModal.vue').default);
Vue.component('medium-create-modal', require('./components/media/MediumCreateModal.vue').default);
Vue.component('medium-export-modal', require('./components/media/MediumExportModal.vue').default);
Vue.component('meeting', require('./components/meeting/Meeting.vue').default);
Vue.component('note-modal', require('./components/note/NoteModal.vue').default);
Vue.component('notes', require('./components/note/Notes.vue').default);
/*Vue.component('objective-medium-modal', require('./components/objectives/ObjectiveMediumModal.vue').default);*/
Vue.component('certificate-generate-modal', require('./components/certificate/GenerateCertificateModal.vue').default);
Vue.component('logbook', require('./components/logbooks/Logbook.vue').default);
Vue.component('logbook-entry-modal', require('./components/logbooks/LogbookEntryModal.vue').default);
Vue.component('subscribe-objective-modal', require('./components/objectives/SubscribeObjectiveModal.vue').default);
Vue.component('objective-progress-subscription-modal', require('./components/objectives/ObjectiveProgressSubscriptionModal.vue').default);
Vue.component('task-modal', require('./components/tasks/TaskModal.vue').default);
Vue.component('task', require('./components/tasks/Task.vue').default);
Vue.component('task-timeline', require('./components/tasks/Timeline.vue').default);
Vue.component('kanbans', require('./components/kanban/Kanbans.vue').default);
Vue.component('kanban-board', require('./components/kanban/KanbanBoard.vue').default);
Vue.component('subscribe-modal', require('./components/subscription/SubscribeModal.vue').default);
Vue.component('sidebar', require('./components/uiElements/Sidebar.vue').default);
Vue.component('move-terminal-objective-modal', require('./components/objectives/MoveTerminalObjectiveModal.vue').default);
Vue.component('prerequisite-modal', require('./components/prerequisites/PrerequisiteObjectiveModal.vue').default);
Vue.component('lms-modal', require('./../../app/Plugins/Lms/resources/js/components/Create.vue').default);
Vue.component('color-picker-component', require('./components/kanban/ColorPickerComponent.vue').default);
Vue.component('color-picker-input', require('./components/kanban/ColorPickerInput.vue').default);


Vue.component('tests-table', require('./components/tests/TestsTable.vue').default);

let tinyMcePlugins = [
    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
    "searchreplace wordcount visualblocks visualchars code fullscreen",
    "insertdatetime media nonbreaking save table directionality",
    "emoticons template paste textpattern example"
];

Vue.prototype.$initTinyMCE = function (tinyMcePlugins) {

    tinymce.remove();
    tinymce.init({
        path_absolute : "/",
        selector: "textarea.my-editor",
        branding:false,
        plugins: tinyMcePlugins,
        external_plugins: {'mathjax': '/node_modules/@dimakorotkov/tinymce-mathjax/plugin.min.js'},
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | mathjax link image media example ",
        relative_urls: false,
        entity_encoding : "raw",

        mathjax: {
            lib: '/node_modules/mathjax/es5/tex-mml-chtml.js', // path to mathjax
            //symbols: {start: '\\(', end: '\\)'}, //optional: mathjax symbols
            //className: "math-tex", //optional: mathjax element class
            //configUrl: '/your-path-to-plugin/@dimakorotkov/tinymce-mathjax/config.js' //optional: mathjax config js
        },

        file_browser_callback : function(field_name, url, type, win) {
            var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;
        }
    });


    tinymce.PluginManager.add('example', function(editor, url) {
        var openDialog = function () {
            document.querySelector("#app").__vue__.$modal.show('medium-create-modal', {'public': 1 });
            $('#medium_id').on('change', function() {
                //reload thumbs
                editor.insertContent('<img src="/media/'+ document.getElementById('medium_id').value +'" width="500">', {format: 'raw'});
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
                    name: 'Curriculum Media Plugin',
                    url: 'http://curriculumonline.de'
                };
            }
        };
    });

};
/**
 * Custom Vue directive "can" to check against permissions.
 * If permission is not given element gets style display:none
 *
 * ! Always check permissions in the backend.
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

Vue.directive('hide-if-permission', function (el, binding) {
    if(window.Laravel.permissions.indexOf(binding.value) !== -1){
        el.style.display = 'none';
    }
});

/**
 * Custom Vue directive "permission" to check against permissions.
 * If permission(s) is/are not given element gets removed from dom
 *
 * ! Always check permissions in the backend.
 * This directive enables shorter syntax on vue.
 *
 * Example:
 * <element v-permission="'curriculum_edit'" ><element>
 * <element v-permission="'content_create, ' + subscribable_type + '_content_create'" ><element>
 *  ! you have to use 'App\\Curriculum_content_create' to get 'App\Curriculum_content_create'
 *
 * @type Vue
 */
Vue.directive('permission', function (el, binding, vnode) {
    let allowed = false;

    binding.value.split(',').forEach(function (permission){
        if(window.Laravel.permissions.indexOf(permission.trim()) !== -1) {
            allowed = true;
        }
    });

    if (allowed == false){
        // replace HTMLElement with comment node
        const comment = document.createComment('removing elements, missing permission(s): ' + binding.value);
        Object.defineProperty(comment, 'setAttribute', {
            value: () => undefined,
        });
        vnode.elm = comment;
        vnode.text = ' ';
        vnode.isComment = true;
        vnode.context = undefined;
        vnode.tag = undefined;
        vnode.data.directives = undefined;

        if (vnode.componentInstance) {
            vnode.componentInstance.$el = comment;
        }

        if (el.parentNode) {
            el.parentNode.replaceChild(comment, el);
        }
    }

});

//global eventHub
Vue.prototype.$eventHub = new Vue();

//global eventHub
Vue.prototype.$eventHub = new Vue();

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
var app = new Vue({
    el: '#app'
});

$(document).ready(function () {
    window._token = $('meta[name="csrf-token"]').attr('content')

    moment.updateLocale('en', {
        week: {dow: 1} // Monday is the first day of the week
    })

    $('.date').datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'en'
    })

    $('.datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        locale: 'en',
        sideBySide: true
    })

    $('.timepicker').datetimepicker({
        format: 'HH:mm:ss'
    })

    $('.select-all').click(function () {
        let $select2 = $(this).parent().siblings('.select2')
        $select2.find('option').prop('selected', 'selected')
        $select2.trigger('change')
    })
    $('.deselect-all').click(function () {
        let $select2 = $(this).parent().siblings('.select2')
        $select2.find('option').prop('selected', '')
        $select2.trigger('change')
    })

    $('.select2').select2()

    $('.treeview').each(function () {
        var shouldExpand = false
        $(this).find('li').each(function () {
            if ($(this).hasClass('active')) {
                shouldExpand = true
            }
        })
        if (shouldExpand) {
            $(this).addClass('active')
        }
    })
})
