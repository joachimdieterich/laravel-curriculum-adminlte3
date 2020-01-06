
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

Vue.prototype.$initTinyMCE = function (options) {
    tinyMCE.remove();
    tinymce.init({
        path_absolute : "/",
        selector: "textarea.my-editor",
        branding:false,
        plugins: [
          "advlist autolink lists link image charmap print preview hr anchor pagebreak",
          "searchreplace wordcount visualblocks visualchars code fullscreen",
          "insertdatetime media nonbreaking save table contextmenu directionality",
          "emoticons template paste textcolor colorpicker textpattern"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
        relative_urls: false,

        file_browser_callback : function(field_name, url, type, win) {
            var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

            var cmsURL =  "/" + 'laravel-filemanager?field_name=' + field_name;
                cmsURL = cmsURL + "&type=Files";

            tinyMCE.activeEditor.windowManager.open({
                  file : cmsURL,
                  title : 'Filemanager',
                  width : x * 0.8,
                  height : y * 0.8,
                  resizable : "yes",
                  close_previous : "no"
            });
        },
    });
}

import VModal from 'vue-js-modal';
Vue.use(VModal);

import Sticky from 'vue-sticky-directive'
Vue.use(Sticky)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */ 
Vue.component('organization-modal', require('./components/organization/OrganizationModal.vue').default);
Vue.component('group-modal', require('./components/group/GroupModal.vue').default);


Vue.component('curriculum-view', require('./components/curriculum/CurriculumView.vue').default);
Vue.component('curriculum-description-modal', require('./components/curriculum/CurriculumDescriptionModal.vue').default);
Vue.component('terminal-objective-modal', require('./components/objectives/TerminalObjectiveModal.vue').default);
Vue.component('enabling-objective-modal', require('./components/objectives/EnablingObjectiveModal.vue').default);
Vue.component('objective-view', require('./components/objectives/ObjectiveView.vue').default);
Vue.component('objective-box', require('./components/objectives/ObjectiveBox.vue').default);
Vue.component('dropdown-button', require('./components/uiElements/DropdownButton.vue').default);
Vue.component('content-modal', require('./components/content/ContentModal.vue').default);
Vue.component('content-create-modal', require('./components/content/ContentCreateModal.vue').default);
Vue.component('medium-modal', require('./components/media/MediumModal.vue').default);
Vue.component('objective-medium-modal', require('./components/objectives/ObjectiveMediumModal.vue').default);
Vue.component('certificate-generate-modal', require('./components/certificate/GenerateCertificateModal.vue').default);

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
    el: '#app', 
      
});    
