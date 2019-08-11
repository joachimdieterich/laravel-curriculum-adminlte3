
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
Vue.use(VModal);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */ 

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));
Vue.component('organization-modal', require('./components/OrganizationModal.vue').default);
Vue.component('group-modal', require('./components/GroupModal.vue').default);


Vue.component('curriculum-view', require('./components/curriculum/CurriculumView.vue').default);
Vue.component('terminal-objective-modal', require('./components/objectives/TerminalObjectiveModal.vue').default);
Vue.component('enabling-objective-modal', require('./components/objectives/EnablingObjectiveModal.vue').default);
Vue.component('objective-box', require('./components/objectives/ObjectiveBox.vue').default);
Vue.component('objective-description-modal', require('./components/objectives/ObjectiveDescriptionModal.vue').default);
Vue.component('dropdown-button', require('./components/uiElements/DropdownButton.vue').default);
Vue.component('content-modal', require('./components/content/ContentModal.vue').default);
Vue.component('medium-modal', require('./components/media/MediumModal.vue').default);
Vue.component('objective-medium-modal', require('./components/objectives/ObjectiveMediumModal.vue').default);

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
    //return window.Laravel.permissions.indexOf(binding.value) !== -1;
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app', 
    
});
