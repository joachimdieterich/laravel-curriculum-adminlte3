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
/**
 *
 * global eventHub
 *
 * How to add a receiver:
 * created() {
 *     this.$eventHub.on('reload_agenda', (params) => {
 *         // do something
 *     });
 * },
 *
 * how to add a sender
 * this.$eventHub.emit('reload_agenda', params);
 * @type {Vue}
 */
import mitt from 'mitt';
app.config.globalProperties.$eventHub = mitt();

import VueDOMPurifyHTML from 'vue-dompurify-html';
app.use(VueDOMPurifyHTML, {
    namedConfigurations: {
        plaintext: {
            USE_PROFILES: { html: false },
        },
    },
    hooks: {
        afterSanitizeAttributes: (currentNode) => {
            if ('rel' in currentNode && currentNode.rel == 'noopener') {
                currentNode.setAttribute('target', '_blank');
            }
        }
    }
});
import { useGlobalStore} from "./store/global";
import { createPinia } from "pinia";
const pinia = createPinia();
app.use(pinia);
const globalStore = useGlobalStore();

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

import VSwatches from 'vue3-swatches';
import 'vue3-swatches/dist/style.css';
app.use(VSwatches);

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

app.config.globalProperties.$decodeHTMLEntities = (text) => {
    return $("<textarea/>")
        .html(text)
        .text();
};


/**
 * Store current ab in global storage
 * example @click="setGlobalStorage('#logbook_'+entry.id, '#logbook_description_'+entry.id)"
 * @param key
 * @param value
 */
app.config.globalProperties.setGlobalStorage = (key, value) => {
    globalStore.setItem(key, value);
};

/**
 * check if current value is set in global storage
 * example: :class="getGlobalStorage('#logbook_'+entry.id, '#logbook_description_'+entry.id)"
 * @param key
 * @param value
 * @param class_string
 * @param is_default_element
 * @returns {string}
 */
app.config.globalProperties.getGlobalStorage = (key, value, class_string = "active", is_default_element = false) => {
    if (globalStore.getItem(key) === value) {
        return class_string;
    }
    if (globalStore.getItem(key) === null && is_default_element === true) {
        return class_string;
    }
};


//-> use permission in vue3 templates, use checkPermission in scripts
app.config.globalProperties.checkPermission = (permission) => {
    return window.Laravel.permissions.indexOf(permission) !== -1;
};

app.config.globalProperties.htmlToText = (html) => {
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
};

/**
 * make userId accessible for vue
 * @type {string}
 */
if (document.querySelector("meta[name='user-id']")){
    app.config.globalProperties.$userId = document.querySelector("meta[name='user-id']").getAttribute('content');
}

import Sticky from 'vue-sticky-directive';

app.use(Sticky);

/*

 */
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

import { defineAsyncComponent } from 'vue';  //use asyncComponents to reduce payload for users

app.component('absence-modal',  defineAsyncComponent(() => import('./components/absence/AbsenceModal.vue')));
app.component('admin-view',  defineAsyncComponent(() => import('./components/admin/AdminView.vue')));
app.component('breadcrumbs',  defineAsyncComponent(() => import('./components/uiElements/Breadcrumbs.vue')));
app.component('certificate-generate-modal', defineAsyncComponent(() => import('./components/certificate/GenerateCertificateModal.vue')));
app.component('certificates', defineAsyncComponent(() => import('./components/certificate/Certificates.vue')));
app.component('certificate', defineAsyncComponent(() => import('./components/certificate/Certificate.vue')));

app.component('config', defineAsyncComponent(() => import('./components/config/Config.vue')));
app.component('configs', defineAsyncComponent(() => import('./components/config/Configs.vue')));
app.component('content-modal', defineAsyncComponent(() => import('./components/content/ContentModal.vue')));
app.component('curriculum',  defineAsyncComponent(() => import('./components/curriculum/Curriculum.vue')));
app.component('curricula',  defineAsyncComponent(() => import('./components/curriculum/Curricula.vue')));

app.component('enabling-objective-modal', defineAsyncComponent(() => import('./components/objectives/EnablingObjectiveModal.vue')));
app.component('events', defineAsyncComponent(() => import('../../app/Plugins/Eventmanagement/eVewa/resources/js/components/embedEvents.vue')));
app.component('exam', defineAsyncComponent(() => import('./components/exam/Exam.vue')));
app.component('exams_list', defineAsyncComponent(() => import('./components/exam/Exams.vue')));
app.component('grades', defineAsyncComponent(() => import('./components/grade/Grades.vue')));
app.component('grade', defineAsyncComponent(() => import('./components/grade/Grade.vue')));

app.component('groups', defineAsyncComponent(() => import('./components/group/Groups.vue')));
app.component('group', defineAsyncComponent(() => import('./components/group/Group.vue')));
app.component('group-modal', defineAsyncComponent(() => import('./components/group/GroupModal.vue')));

app.component('leaflet-map', defineAsyncComponent(() => import('./components/map/Map.vue'))); //cannot be "map" -> name map is reserved
app.component('link-item', defineAsyncComponent(() => import('./components/uiElements/LinkItem.vue')));
app.component('logbook', defineAsyncComponent(() => import('./components/logbook/Logbook.vue')));
app.component('logbooks', defineAsyncComponent(() => import('./components/logbook/Logbooks.vue')));
app.component('logbook-entry-modal', defineAsyncComponent(() => import('./components/logbookEntry/LogbookEntryModal.vue')));
app.component('logbook-entry-subject-modal', defineAsyncComponent(() => import('./components/logbookEntry/LogbookEntrySubjectModal.vue')));

app.component('reference-objective-modal', defineAsyncComponent(() => import('./components/reference/ReferenceObjectiveModal.vue')));

app.component('maps', defineAsyncComponent(() => import('./components/map/Maps.vue')));
app.component('media-renderer', defineAsyncComponent(() => import('./components/media/MediaRenderer.vue')));
app.component('medium-modal', defineAsyncComponent(() => import('./components/media/MediumModal.vue')));
app.component('medium-export-modal', defineAsyncComponent(() => import('./components/media/MediumExportModal.vue')));
app.component('meeting', defineAsyncComponent(() => import('./components/meeting/Meeting.vue')));
app.component('meetings', defineAsyncComponent(() => import('./components/meeting/Meetings.vue')));
app.component('metadatasets', defineAsyncComponent(() => import('./components/metadataset/Metadatasets.vue')));
app.component('model-limiter', defineAsyncComponent(() => import('./components/config/ModelLimiter.vue')));

app.component('navigators', defineAsyncComponent(() => import('./components/navigator/Navigators.vue')));
app.component('navigator', defineAsyncComponent(() => import('./components/navigator/Navigator.vue')));

app.component('note-modal', defineAsyncComponent(() => import('./components/note/NoteModal.vue')));
//app.component('notes', defineAsyncComponent(() => import('./components/note/Notes.vue')));

app.component('objective', defineAsyncComponent(() => import('./components/objectives/Objective.vue')));
app.component('objective-box', defineAsyncComponent(() => import('./components/objectives/ObjectiveBox.vue')));
app.component('objective-progress-subscription-modal', defineAsyncComponent(() => import('./components/objectives/ObjectiveProgressSubscriptionModal.vue')));

app.component('objective-types', defineAsyncComponent(() => import('./components/objectiveType/ObjectiveTypes.vue')));
app.component('objective-type', defineAsyncComponent(() => import('./components/objectiveType/ObjectiveType.vue')));
app.component('objective-type-modal', defineAsyncComponent(() => import('./components/objectiveType/ObjectiveTypeModal.vue')));


app.component('organizations', defineAsyncComponent(() => import('./components/organization/Organizations.vue')));
app.component('organization', defineAsyncComponent(() => import('./components/organization/Organization.vue')));
app.component('organization-modal', defineAsyncComponent(() => import('./components/organization/OrganizationModal.vue')));

app.component('organization-types', defineAsyncComponent(() => import('./components/organizationType/OrganizationTypes.vue')));
app.component('organization-type', defineAsyncComponent(() => import('./components/organizationType/OrganizationType.vue')));

app.component('kanbans', defineAsyncComponent(() => import('./components/kanban/Kanbans.vue')));
app.component('kanban', defineAsyncComponent(() => import('./components/kanban/Kanban.vue')));

app.component('move-terminal-objective-modal', defineAsyncComponent(() => import('./components/objectives/MoveTerminalObjectiveModal.vue')));
app.component('prerequisite-modal', defineAsyncComponent(() => import('./components/prerequisites/PrerequisiteObjectiveModal.vue')));
//app.component('lms-modal', defineAsyncComponent(() => import('./../../app/Plugins/Lms/resources/js/components/Create.vue')));
//app.component('color-picker-input', defineAsyncComponent(() => import('./components/kanban/ColorPickerInput.vue'))); // not used anymore ->

app.component('searchbar', defineAsyncComponent(() => import('./components/uiElements/Searchbar.vue')));
//app.component('set-achievements-modal', defineAsyncComponent(() => import('./components/plan/SetAchievementsModal.vue')));
app.component('sidebar', defineAsyncComponent(() => import('./components/uiElements/Sidebar.vue')));

app.component('subscribe-objective-modal', defineAsyncComponent(() => import('./components/objectives/SubscribeObjectiveModal.vue')));
app.component('subjects', defineAsyncComponent(() => import('./components/subject/Subjects.vue')));
app.component('subject', defineAsyncComponent(() => import('./components/subject/Subject.vue')));

app.component('tests-table', defineAsyncComponent(() => import('./components/tests/TestsTable.vue')));
app.component('title-component', defineAsyncComponent(() => import('./components/uiElements/Title.vue')));

app.component('task-modal', defineAsyncComponent(() => import('./components/task/TaskModal.vue')));
app.component('task', defineAsyncComponent(() => import('./components/task/Task.vue')));
app.component('tasks', defineAsyncComponent(() => import('./components/task/Tasks.vue')));

app.component('terminal-objective-modal', defineAsyncComponent(() => import('./components/objectives/TerminalObjectiveModal.vue')));
app.component('training', defineAsyncComponent(() => import('./components/training/Training.vue')));


app.component('permissions', defineAsyncComponent(() => import('./components/permission/Permissions.vue')));
app.component('permission', defineAsyncComponent(() => import('./components/permission/Permission.vue')));

app.component('periods', defineAsyncComponent(() => import('./components/period/Periods.vue')));
app.component('period', defineAsyncComponent(() => import('./components/period/Period.vue')));

app.component('plan', defineAsyncComponent(() => import('./components/plan/Plan.vue')));
app.component('plans', defineAsyncComponent(() => import('./components/plan/Plans.vue')));


app.component('roles', defineAsyncComponent(() => import('./components/role/Roles.vue')));
app.component('role', defineAsyncComponent(() => import('./components/role/Role.vue')));
app.component('user', defineAsyncComponent(() => import('./components/user/User.vue')));
app.component('users', defineAsyncComponent(() => import('./components/user/Users.vue')));

app.component('variant-definition-modal', defineAsyncComponent(() => import('./components/variantDefinition/VariantDefinitionModal.vue')));
app.component('variant-definition', defineAsyncComponent(() => import('./components/variantDefinition/VariantDefinition.vue')));
app.component('variant-definitions', defineAsyncComponent(() => import('./components/variantDefinition/VariantDefinitions.vue')));


app.component('videoconference', defineAsyncComponent(() => import('./components/videoconference/Videoconference.vue')));
app.component('videoconferences', defineAsyncComponent(() => import('./components/videoconference/Videoconferences.vue')));

app.config.globalProperties.$initTinyMCE = function(
    tinyMcePlugins,
    attr = null,
    customToolbar1 = null,
    customToolbar2 = null,
    extended_valid_elements = null,
    height = 200,
) {

    const defaultPlugins = [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table directionality",
        "emoticons template paste textpattern curriculummedia",
        "autoresize"
    ];

    return {// allows adding additional attributes for specific cases
        // attributes can be overwritten if they are set BEFORE this line
        ...attr,
        path_absolute : "/",
        selector: "textarea.my-editor",
        branding: false,
        placeholder: attr?.placeholder ?? this.trans('global.description'),
        plugins: tinyMcePlugins ?? defaultPlugins,
        external_plugins: { mathjax: '/node_modules/@dimakorotkov/tinymce-mathjax/plugin.min.js' },
        menubar: "edit format",
        toolbar1: customToolbar1 ?? "bold underline italic | alignleft aligncenter alignright alignjustify",
        toolbar2: customToolbar2 ?? "bullist numlist outdent indent | curriculummedia link",
        extended_valid_elements: extended_valid_elements ?? '',
        default_link_target:"_blank",
        relative_urls: false,
        entity_encoding: "raw",
        language: 'de',
        height: height,

        mathjax: {
            lib: '/node_modules/mathjax/es5/tex-mml-chtml.js', // path to mathjax
        },
        setup: function (editor) {
            editor.ui.registry.addButton('insertFirstname', {
                text: window.trans.global.firstname,
                onAction: function (_) {
                    editor.insertContent('<span id="firstname" style="background-color: lightgray;">'+ window.trans.global.firstname + '</span>&nbsp;');
                }
            });
            editor.ui.registry.addButton('insertLastname', {
                text: window.trans.global.lastname,
                onAction: function (_) {
                    editor.insertContent('<span id="lastname" style="background-color: lightgray;">'+ window.trans.global.lastname + '</span>&nbsp;');
                }
            });
            editor.ui.registry.addButton('organizationTitle', {
                text: window.trans.global.organization.title_singular,
                onAction: function (_) {
                    editor.insertContent('<span id="organization_title" style="background-color: lightgray;">'+ window.trans.global.organization.title_singular + '</span>&nbsp;');
                }
            });
            editor.ui.registry.addButton('organizationStreet', {
                text: window.trans.global.organization.fields.street,
                onAction: function (_) {
                    editor.insertContent('<span id="organization_street" style="background-color: lightgray;">'+ window.trans.global.organization.fields.street + '</span>&nbsp;');
                }
            });
            editor.ui.registry.addButton('organizationPostcode', {
                text: window.trans.global.organization.fields.postcode,
                onAction: function (_) {
                    editor.insertContent('<span id="organization_postcode" style="background-color: lightgray;">'+ window.trans.global.organization.fields.postcode + '</span>&nbsp;');
                }
            });
            editor.ui.registry.addButton('organizationCity', {
                text: window.trans.global.organization.fields.city,
                onAction: function (_) {
                    editor.insertContent('<span id="organization_city" style="background-color: lightgray;">'+ window.trans.global.organization.fields.city + '</span>&nbsp;');
                }
            });
            editor.ui.registry.addButton('certificateDate', {
                text: window.trans.global.date,
                onAction: function (_) {
                    editor.insertContent('<span id="date" style="background-color: lightgray;">'+ window.trans.global.date + '</span>&nbsp;');
                }
            });
            editor.ui.registry.addButton('usersProgress', {
                text: window.trans.global.progress.title_singular,
                onAction: function (_) {
                    document.querySelector("#app").__vue__.$modal.show('objective-progress-subscription-modal');
                    $('#progress_reference').on('change', function() {
                        const progress_reference = JSON.parse(document.getElementById('progress_reference').value);
                        editor.selection.setContent('<span reference_type="' + progress_reference.referenceable_type + '" reference_id="'+ ( Array.isArray(progress_reference.referenceable_id) ? progress_reference.referenceable_id.join() : progress_reference.referenceable_id) +'" min_value="'+ progress_reference.percentage +'"/>'   +  tinymce.activeEditor.selection.getContent() + '</span>', {format: 'raw'});
                    });
                }
            });

            editor.ui.registry.addButton('curriculummedia',  {
                text: 'Medien',
                icon: 'image',
                tooltip: 'Medien',
                onAction: function () {
                    globalStore.showModal('medium-modal', attr);
                    app.config.globalProperties.$eventHub.on('insertContent', (event) => {
                        console.log(event);
                        if (attr.callbackId == event.id) {
                            let html = '';
                            globalStore.selectedMedia.forEach((media) => {
                                html = html.concat(
                                    editor.insertContent(
                                        '<img src="/media/'+ media.id +'?preview=true" width="500">',
                                        {format: 'raw'}
                                    )
                                );
                            });
                            app.config.globalProperties.$eventHub.off('insertContent'); //remove listender
                            globalStore.setSelectedMedia(null); // deselect
                            return html;
                        }
                    });
                }
            });
        },
    };
};

/**
 * Global Datatable options
 */
app.config.globalProperties.$dtOptions = {
    dom: 'tilpr',
    pageLength: 10,
    language: {
        url: '/datatables/i18n/German.json',
        paginate: {
            "first":      '<i class="fa fa-angle-double-left"></id>',
            "last":       '<i class="fa fa-angle-double-right"></id>',
            "next":       '<i class="fa fa-angle-right"></id>',
            "previous":   '<i class="fa fa-angle-left"></id>',
        },
    },
    select: true
};

/**
 * Medium Modal Params
 */
/*app.config.globalProperties.$mediumModalParams  = {
    accept: '',
    callbackFunction: null,
    callbackParentComponent: null,
    callbackComponent: null,
    eventHubCallbackFunction: null,
    eventHubCallbackFunctionParams: null,
    public: 0,
    repository: 'local',
    subscribable_type: null,
    subscribable_id: null,
    subscribeSelected: false,
    target: 'medium_id',
},*/
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
    if (window.Laravel.permissions.indexOf(binding.value) == -1) {
        el.style.display = 'none';
    }
    return window.Laravel.permissions.indexOf(binding.value) !== -1;
});

app.directive('hide-if-permission', function (el, binding) {
    if (window.Laravel.permissions.indexOf(binding.value) !== -1) {
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
 * Examples:
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

app.directive("inline", (element) => {
    element.replaceWith(...element.children);
});

/**
* Decodes html entities (used in datatable/list-results)
**/
app.config.globalProperties.$decodeHtml = (str) =>  {
    let map =
        {
            '&amp;': '&',
            '&lt;': '<',
            '&gt;': '>',
            '&quot;': '"',
            '&#039;': "'"
        };
    if (str !== null) {
        return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
    } else {
        return '';
    }

};

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
