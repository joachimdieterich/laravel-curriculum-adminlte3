
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');
require('tinymce/tinymce');
//require('leaflet/dist/leaflet.js');
require('@activix/bootstrap-datetimepicker');

//vue
window.Vue = require('vue').default;

window.moment = require('moment');

//security
import { buildVueDompurifyHTMLDirective } from 'vue-dompurify-html';
import DOMPurify from 'dompurify';
const createWrapper = (inner) => {
    return (el, binding) => {
        if (binding.value === undefined || binding.value === null) {
            return;
        }
        inner(el, binding);
    };
};
const directive = buildVueDompurifyHTMLDirective({}, () => DOMPurify);
Vue.directive(
    'dompurify-html',
    {
        inserted: createWrapper(directive.inserted),
        update: createWrapper(directive.update),
        unbind(el) {
            el.innerHTML = '';
        },
    }
);

//broadcasting
import VueEcho from 'vue-echo';
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
}

// use trans function like in blade
import _ from 'lodash'; //needed to get

//todo: explain function
Vue.prototype.trans = (key) => {
    return _.get(window.trans, key, _.get(window.trans, 'global.' + key.split(".").splice(-1), key) );
};

import "vue-swatches/dist/vue-swatches.css";


Vue.prototype.$textcolor = (color, dark = '#000', light = '#fff') => {
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
        //console.log('black');
        return dark;

    } else {
        //console.log('white');
        return light;
    }
};


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

Vue.prototype.checkPermission = (permission) => {
    return window.Laravel.permissions.indexOf(permission) !== -1;
};

/**
 * Gets the text content of the HTML
 * No HTML tags are included, but any text, including the text inside the tags, is included.
 *
 * @param {string} html Input HTML
 * @returns The text content of the HTML
 */
Vue.prototype.htmlToText = (html) => {
    var txt = document.createElement("textarea");
    txt.innerHTML = html ?? '';
    let map =
        {
            '&amp;': '&',
            '&lt;': '<',
            '&gt;': '>',
            '&quot;': '"',
            '&#039;': "'",
            '&nbsp;': " ",
            '&szlig;': "ß",
            '&Auml;': 'Ä',
            '&auml;': 'ä',
            '&Uuml;': 'Ü',
            '&uuml;': 'ü',
            '&Ouml;': 'Ö',
            '&ouml;': 'ö',
            '&copy;': '©',
        };

    txt =  txt.value.replace(/&amp;|&lt;|&gt;|&quot;|&#039;|&nbsp;|&szlig;|&Auml;|&auml;|&Uuml;|&uuml;|&Ouml;|&ouml;|&copy;/g, function(m) {return map[m];})
        .replace(/(?:\r\n|\r|\n)/g, '<br>')
        .replace('<br>', ' ');

    return txt.replace(/(&lt;|<)script.*?(&lt;|<)\/script(&gt;|>)|<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, ' ');

}

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
Vue.component('organization-modal', () => import('./components/organization/OrganizationModal.vue'));
Vue.component('group-modal', () => import('./components/group/GroupModal.vue'));

Vue.component('admin-view', () => import('./components/admin/AdminView.vue'));
Vue.component('absence-modal', () => import('./components/absence/AbsenceModal.vue'));
Vue.component('curriculum-view', () => import('./components/curriculum/CurriculumView.vue'));
Vue.component('group-view', () => import('./components/group/GroupView.vue'));
Vue.component('terminal-objective-modal', () => import('./components/objectives/TerminalObjectiveModal.vue'));
Vue.component('data-table-widgets', () => import('./components/uiElements/DataTableWidgets.vue'));
Vue.component('enabling-objective-modal', () => import('./components/objectives/EnablingObjectiveModal.vue'));
Vue.component('events', () => import('../../app/Plugins/Eventmanagement/eVewa/resources/js/components/embedEvents'));
Vue.component('objective-view', () => import('./components/objectives/ObjectiveView.vue'));
Vue.component('objective-box', () => import('./components/objectives/ObjectiveBox.vue'));
Vue.component('dropdown-button', () => import('./components/uiElements/DropdownButton.vue'));
Vue.component('model-limiter', () => import('./components/config/ModelLimiter.vue'));
Vue.component('content-modal', () => import('./components/content/ContentModal.vue'));
Vue.component('content-create-modal', () => import('./components/content/ContentCreateModal.vue'));
Vue.component('content-subscription-modal', () => import('./components/content/ContentSubscriptionModal.vue'));
Vue.component('curricula', () => import('./components/curriculum/Curricula.vue'));
Vue.component('reference-objective-modal', () => import('./components/reference/ReferenceObjectiveModal.vue'));
Vue.component('maps', () => import('./components/map/Maps.vue'));
Vue.component('media-renderer', () => import('./components/media/MediaRenderer.vue'));
Vue.component('medium-modal', () => import('./components/media/MediumModal.vue'));
Vue.component('medium-create-modal', () => import('./components/media/MediumCreateModal.vue'));
Vue.component('medium-export-modal', () => import('./components/media/MediumExportModal.vue'));
Vue.component('meeting', () => import('./components/meeting/Meeting.vue'));
Vue.component('note-modal', () => import('./components/note/NoteModal.vue'));
Vue.component('notes', () => import('./components/note/Notes.vue'));
/*Vue.component('objective-medium-modal', () => import('./components/objectives/ObjectiveMediumModal.vue'));*/
Vue.component('certificate-generate-modal', () => import('./components/certificate/GenerateCertificateModal.vue'));
Vue.component('logbook', () => import('./components/logbooks/Logbook.vue'));
Vue.component('logbooks', () => import('./components/logbooks/Logbooks.vue'));
Vue.component('logbook-entry-modal', () => import('./components/logbooks/LogbookEntryModal.vue'));
Vue.component('logbook-entry-subject-modal', () => import('./components/logbooks/LogbookEntrySubjectModal.vue'));
Vue.component('plan', () => import('./components/plan/Plan.vue'));
Vue.component('plans', () => import('./components/plan/Plans.vue'));
Vue.component('plan-achievements', () => import('./components/plan/PlanAchievements.vue'));
Vue.component('subscribe-objective-modal', () => import('./components/objectives/SubscribeObjectiveModal.vue'));
Vue.component('set-achievements-modal', () => import('./components/plan/SetAchievementsModal.vue'));
Vue.component('plan-achievements-options-modal', () => import('./components/plan/PlanAchievementsOptionsModal.vue'));
Vue.component('select-users-modal', () => import('./components/users/SelectUsersModal.vue'));
Vue.component('objective-progress-subscription-modal', () => import('./components/objectives/ObjectiveProgressSubscriptionModal.vue'));
Vue.component('task-modal', () => import('./components/tasks/TaskModal.vue'));
Vue.component('task', () => import('./components/tasks/Task.vue'));
Vue.component('task-timeline', () => import('./components/tasks/Timeline.vue'));
Vue.component('training', () => import('./components/training/Training'));
Vue.component('kanbans', () => import('./components/kanban/Kanbans.vue'));
Vue.component('kanban-board', () => import('./components/kanban/KanbanBoard.vue'));
Vue.component('subscribe-modal', () => import('./components/subscription/SubscribeModal.vue'));
Vue.component('sidebar', () => import('./components/uiElements/Sidebar.vue'));
Vue.component('move-terminal-objective-modal', () => import('./components/objectives/MoveTerminalObjectiveModal.vue'));
Vue.component('prerequisite-modal', () => import('./components/prerequisites/PrerequisiteObjectiveModal.vue'));
Vue.component('lms-modal', () => import('./../../app/Plugins/Lms/resources/js/components/Create.vue'));
Vue.component('color-picker-component', () => import('./components/kanban/ColorPickerComponent.vue'));
Vue.component('color-picker-input', () => import('./components/kanban/ColorPickerInput.vue'));
Vue.component('leaflet-map', () => import('./components/map/Map.vue'));
Vue.component('searchbar', () => import('./components/uiElements/Searchbar.vue'));
Vue.component('date-picker-wrapper', () => import('./components/uiElements/DatePickerWrapper.vue'));
Vue.component('videoconference', () => import('./components/videoconference/Videoconference.vue'));
Vue.component('videoconferences', () => import('./components/videoconference/Videoconferences.vue'));

Vue.component('tests-table', () => import('./components/tests/TestsTable.vue'));

Vue.prototype.$initTinyMCE = function (tinyMcePlugins, attr = null, customToolbar1 = null, customToolbar2 = null, extended_valid_elements = null) {

    const defaultPlugins = [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table directionality",
        "emoticons template paste textpattern example"
    ];

    tinymce.remove();
    tinymce.init({
        // allows adding additional attributes for specific cases
        // attributes can be overwritten if they are set BEFORE this line
        ...attr,

        path_absolute : "/",
        selector: "textarea.my-editor",
        branding: false,
        plugins: tinyMcePlugins ?? defaultPlugins,
        external_plugins: {'mathjax': '/node_modules/@dimakorotkov/tinymce-mathjax/plugin.min.js'},
        menubar: "edit format",
        toolbar1: customToolbar1 ?? "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify",
        toolbar2: customToolbar2 ?? "bullist numlist outdent indent | example mathjax link image media code",
        extended_valid_elements: extended_valid_elements ?? '',
        font_size_input_default_unit: "pt",
        relative_urls: false,
        entity_encoding : "raw",
        language: 'de',

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
            document.querySelector("#app").__vue__.$modal.show('medium-create-modal', attr);

            if (!document.querySelector("#app").__vue__.$eventHub._events.insertContent){
                document.querySelector("#app").__vue__.$eventHub.$on('insertContent', (event) => {
                    console.log(event);
                    if (attr.eventHubCallbackFunctionParams == event.id) {
                        editor.insertContent('<img src="/media/'+ event.selectedMediumId +'?preview=true" width="500">', {format: 'raw'});
                    }
                    document.querySelector("#app").__vue__.$eventHub._events.insertContent = undefined; //destroy listener to prevent multiple inserts on 2nd, 3rd.. time
                });
            }
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
