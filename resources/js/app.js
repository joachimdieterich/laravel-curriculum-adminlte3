/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');
require('@activix/bootstrap-datetimepicker');

//vue
import { createApp } from 'vue';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const app = createApp({});
//window.Vue = app;

window.moment = require('moment');

//broadcasting
/*import VueEcho from 'vue-echo';
if (process.env.MIX_PUSHER_APP_ACTIVE == 'true') {
    Vue.use(VueEcho, {
        broadcaster: 'pusher',
        key: process.env.MIX_PUSHER_APP_KEY,
        cluster: process.env.MIX_PUSHER_APP_CLUSTER,
        useTLS: process.env.MIX_PUSHER_APP_USE_TLS === 'true',
        forceTLS: process.env.MIX_PUSHER_APP_FORCE_TLS === 'true',
        encrypted: process.env.MIX_PUSHER_APP_ENCRYPTED === 'true',
        wsHost: window.location.hostname,
        wssHost: window.location.hostname,
        wsPort: process.env.MIX_PUSHER_APP_WSPORT,
        wssPort: process.env.MIX_PUSHER_APP_WSSPORT,
        enableTransports: ['ws', 'wss']
    });
}*/

// use trans function like in blade
import _ from 'lodash'; //needed to get

//todo: explain function
app.config.globalProperties.trans = (key) => {
    return _.get(window.trans, key, _.get(window.trans, 'global.' + key.split(".").splice(-1), key) );
};

import "vue-swatches/dist/vue-swatches.css";

app.config.globalProperties.$textcolor = (color, dark = '#000', light = '#fff') => {
    if (typeof(color) != 'string'){
        color = 'ffffff';
    }

    color = (color.charAt(0) === '#') ? color.substring(1, 7) : color;
    //console.log(color);
    var r = parseInt(color.substring(0, 2), 16); // hexToR
    var g = parseInt(color.substring(2, 4), 16); // hexToG
    var b = parseInt(color.substring(4, 6), 16); // hexToB
    //console.log(r + ' ' + g + ' ' + b);
    if (((r * 0.299) + (g * 0.587) + (b * 0.114)) > 140)
    {
        return dark;
    } else {
        return light;
    }
};


/**
 * Store current ab in browser storage
 * example @click="setLocalStorage('#logbook_'+entry.id, '#logbook_description_'+entry.id)"
 * @param key
 * @param value
 */
app.config.globalProperties.setLocalStorage = (key, value) => {
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
app.config.globalProperties.checkLocalStorage = (key, value, class_string = "active", is_default_element = false) => {
    if (localStorage.getItem(key) === value) {
        return class_string;
    }
    if (localStorage.getItem(key) === null && is_default_element === true) {
        return class_string;
    }
};

//-> use permission in vue3 templates, use checkPermission in scripts
app.config.globalProperties.checkPermission = (permission) => {
    return window.Laravel.permissions.indexOf(permission) !== -1;
};

app.config.globalProperties.htmlToText = (html) => {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value.replace(/(&lt;|<)script.*?(&lt;|<)\/script(&gt;|>)|<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
};

/**
 * make userId accessible for vue
 * @type {string}
 */
if (document.querySelector("meta[name='user-id']")){
    app.config.globalProperties.$userId = document.querySelector("meta[name='user-id']").getAttribute('content');
}

/*import VModal from 'vue-js-modal';
app.use(VModal, {dynamic: true});*/


/*import DataTable from 'laravel-vue-datatable';
app.use(DataTable);*/

import Sticky from 'vue-sticky-directive';

app.use(Sticky);

import Toast from 'vue-toastification';

import 'vue-toastification/dist/index.css';

app.use(Toast, {
    transition: "Vue-Toastification__bounce",
    maxToasts: 20,
    newestOnTop: true
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

import { defineAsyncComponent } from 'vue';

app.component('absence-modal',  defineAsyncComponent(() => import('./components/absence/AbsenceModal.vue')));
app.component('admin-view',  defineAsyncComponent(() => import('./components/admin/AdminView.vue')));
app.component('certificate-generate-modal', defineAsyncComponent(() => import('./components/certificate/GenerateCertificateModal.vue')));
app.component('curricula',  defineAsyncComponent(() => import('./components/curriculum/Curricula.vue')));
app.component('curriculum-view', defineAsyncComponent(() => import('./components/curriculum/CurriculumView.vue')));

app.component('content-modal', defineAsyncComponent(() => import('./components/content/ContentModal.vue')));
app.component('content-create-modal', defineAsyncComponent(() => import('./components/content/ContentCreateModal.vue')));
app.component('content-subscription-modal', defineAsyncComponent(() => import('./components/content/ContentSubscriptionModal.vue')));
app.component('organization-modal', defineAsyncComponent(() => import('./components/organization/OrganizationModal.vue')));
app.component('group-modal', defineAsyncComponent(() => import('./components/group/GroupModal.vue')));

app.component('group-view', defineAsyncComponent(() => import('./components/group/GroupView.vue')));
app.component('terminal-objective-modal', defineAsyncComponent(() => import('./components/objectives/TerminalObjectiveModal.vue')));
app.component('data-table-widgets', defineAsyncComponent(() => import('./components/uiElements/DataTableWidgets.vue')));
app.component('enabling-objective-modal', defineAsyncComponent(() => import('./components/objectives/EnablingObjectiveModal.vue')));
app.component('events', defineAsyncComponent(() => import('../../app/Plugins/Eventmanagement/eVewa/resources/js/components/embedEvents')));
app.component('objective-view', defineAsyncComponent(() => import('./components/objectives/ObjectiveView.vue')));
app.component('objective-box', defineAsyncComponent(() => import('./components/objectives/ObjectiveBox.vue')));
app.component('dropdown-button', defineAsyncComponent(() => import('./components/uiElements/DropdownButton.vue')));
app.component('model-limiter', defineAsyncComponent(() => import('./components/config/ModelLimiter.vue')));

app.component('reference-objective-modal', defineAsyncComponent(() => import('./components/reference/ReferenceObjectiveModal.vue')));
app.component('maps', defineAsyncComponent(() => import('./components/map/Maps.vue')));
app.component('media-renderer', defineAsyncComponent(() => import('./components/media/MediaRenderer.vue')));
app.component('medium-modal', defineAsyncComponent(() => import('./components/media/MediumModal.vue')));
app.component('medium-create-modal', defineAsyncComponent(() => import('./components/media/MediumCreateModal.vue')));
app.component('medium-export-modal', defineAsyncComponent(() => import('./components/media/MediumExportModal.vue')));
app.component('meeting', defineAsyncComponent(() => import('./components/meeting/Meeting.vue')));
app.component('note-modal', defineAsyncComponent(() => import('./components/note/NoteModal.vue')));
app.component('notes', defineAsyncComponent(() => import('./components/note/Notes.vue')));
app.component('organizations', defineAsyncComponent(() => import('./components/organization/Organizations.vue')));
app.component('organization', defineAsyncComponent(() => import('./components/organization/Organization.vue')));

app.component('logbook', defineAsyncComponent(() => import('./components/logbooks/Logbook.vue')));
app.component('logbooks', defineAsyncComponent(() => import('./components/logbooks/Logbooks.vue')));
app.component('logbook-entry-modal', defineAsyncComponent(() => import('./components/logbooks/LogbookEntryModal.vue')));
app.component('logbook-entry-subject-modal', defineAsyncComponent(() => import('./components/logbooks/LogbookEntrySubjectModal.vue')));
app.component('plan', defineAsyncComponent(() => import('./components/plan/Plan.vue')));
app.component('subscribe-objective-modal', defineAsyncComponent(() => import('./components/objectives/SubscribeObjectiveModal.vue')));
app.component('set-achievements-modal', defineAsyncComponent(() => import('./components/plan/SetAchievementsModal.vue')));
app.component('objective-progress-subscription-modal', defineAsyncComponent(() => import('./components/objectives/ObjectiveProgressSubscriptionModal.vue')));
app.component('task-modal', defineAsyncComponent(() => import('./components/tasks/TaskModal.vue')));
app.component('task', defineAsyncComponent(() => import('./components/tasks/Task.vue')));
app.component('task-timeline', defineAsyncComponent(() => import('./components/tasks/Timeline.vue')));
app.component('training', defineAsyncComponent(() => import('./components/training/Training')));
app.component('kanbans', defineAsyncComponent(() => import('./components/kanban/Kanbans.vue')));
app.component('kanban-board', defineAsyncComponent(() => import('./components/kanban/KanbanBoard.vue')));
//app.component('subscribe-modal', defineAsyncComponent(() => import('./components/subscription/SubscribeModal.vue')));
app.component('sidebar', defineAsyncComponent(() => import('./components/uiElements/Sidebar.vue')));
app.component('move-terminal-objective-modal', defineAsyncComponent(() => import('./components/objectives/MoveTerminalObjectiveModal.vue')));
app.component('prerequisite-modal', defineAsyncComponent(() => import('./components/prerequisites/PrerequisiteObjectiveModal.vue')));
app.component('lms-modal', defineAsyncComponent(() => import('./../../app/Plugins/Lms/resources/js/components/Create.vue')));
app.component('color-picker-component', defineAsyncComponent(() => import('./components/kanban/ColorPickerComponent.vue')));
app.component('color-picker-input', defineAsyncComponent(() => import('./components/kanban/ColorPickerInput.vue')));
app.component('leaflet-map', defineAsyncComponent(() => import('./components/map/Map.vue')));
app.component('searchbar', defineAsyncComponent(() => import('./components/uiElements/Searchbar.vue')));
app.component('date-picker-wrapper', defineAsyncComponent(() => import('./components/uiElements/DatePickerWrapper.vue')));
app.component('videoconference', defineAsyncComponent(() => import('./components/videoconference/Videoconference.vue')));
app.component('videoconferences', defineAsyncComponent(() => import('./components/videoconference/Videoconferences.vue')));
app.component('tests-table', defineAsyncComponent(() => import('./components/tests/TestsTable.vue')));


app.config.globalProperties.$initTinyMCE = function (tinyMcePlugins, attr = null) {

    const defaultPlugins = [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table directionality",
        "emoticons template paste textpattern curriculummedia"
    ];


    return {// allows adding additional attributes for specific cases
        // attributes can be overwritten if they are set BEFORE this line
        ...attr,

        path_absolute : "/",
        selector: "textarea.my-editor",
        branding:false,
        plugins: tinyMcePlugins ?? defaultPlugins,
        external_plugins: {'mathjax': '/node_modules/@dimakorotkov/tinymce-mathjax/plugin.min.js'},
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | curriculummedia mathjax link image media ",
        relative_urls: false,
        entity_encoding : "raw",
        language: 'de',

        mathjax: {
            lib: '/node_modules/mathjax/es5/tex-mml-chtml.js', // path to mathjax
        }};
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

app.directive('can', function (el, binding) {
    if(window.Laravel.permissions.indexOf(binding.value) == -1){
        el.style.display = 'none';
    }
    return window.Laravel.permissions.indexOf(binding.value) !== -1;
});

app.directive('hide-if-permission', function (el, binding) {
    if(window.Laravel.permissions.indexOf(binding.value) !== -1){
        el.style.display = 'none';
    }
});

/**
 * Custom Vue directive "permission" to check against permissions.
 * csv with permissions.
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
app.directive('permission', function (el, binding, vnode) {
        let allowed = false;

        binding.value.split(',').forEach(function (permission){
            if(window.Laravel.permissions.indexOf(permission.trim()) !== -1) {
                allowed = true;
            }
        });

        if (allowed == false){
            if (el.parentNode) {
                el.parentNode.removeChild(vnode.el);
            }
        }

});

/**
 *
 * global eventHub
 *
 * How to add a receiver:
 * created() {
 *     this.$eventHub.$on('reload_agenda', (params) => {
 *         // do something
 *     });
 * },
 *
 * how to add a sender
 * this.$eventHub.$emit('reload_agenda', params);
 * @type {Vue}
 */
import mitt from 'mitt';
app.config.globalProperties.$eventHub = mitt();

//mount vue
app.mount('#app');

$(document).ready(function () {
    window._token = $('meta[name="csrf-token"]').attr('content');

    moment.updateLocale('en', {
        week: {dow: 1} // Monday is the first day of the week
    });

    $('.date').datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'en'
    });

    $('.datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        locale: 'en',
        sideBySide: true
    });

    $('.timepicker').datetimepicker({
        format: 'HH:mm:ss'
    });

    $('.select-all').click(function () {
        let $select2 = $(this).parent().siblings('.select2');
        $select2.find('option').prop('selected', 'selected');
        $select2.trigger('change');
    });
    $('.deselect-all').click(function () {
        let $select2 = $(this).parent().siblings('.select2');
        $select2.find('option').prop('selected', '');
        $select2.trigger('change');
    });

    $('.select2').select2();

    $('.treeview').each(function () {
        var shouldExpand = false;
        $(this).find('li').each(function () {
            if ($(this).hasClass('active')) {
                shouldExpand = true;
            }
        });
        if (shouldExpand) {
            $(this).addClass('active');
        }
    });
});
