<template>
    <c-select id="subscribe-c-select"
              model="user"
              url="/users/list/subscription"
              @selectedValue="(selectedOption) => {return this.$emit('selectedValue', selectedOption);}"
              :multiple="false"
              :groupedOptions="true"
    >
        <template v-slot:option="option">
            <div class="select-option">
                <div class="select-option select-user">
                    <span class="select-user-avatar">
                        <img class="v-select-icon img-circle color-white select-user-avatar" alt="" :src="option.option.value.icon">
                    </span>
                    <span class="select-option-name">{{ option.option.label }}</span>
                </div>
                <span class="select-option-organization-role" style="margin-left: auto; order: 3;">
                    <span v-for="organization in option.option.value.organizations"
                          class="small select-option-organization"
                    >
                        {{ organization }}
                    </span>
                    <br>
                    <span class="select-option-role">{{ option.option.value.roleName }}</span>
                </span>
            </div>
        </template>
    </c-select>

</template>

<script>
import Avatar from "../uiElements/Avatar.vue";
import CSelect from "../forms/Select.vue";

export default {
    name: "SubscribeUserSelect",
    components: {CSelect, Avatar},
    emits: [
        'selectedValue'
    ],
    data() {
        return {
            componentId: this.$.uid,
        }
    },
}
</script>

<style>
:root {
    --select-option-role-color: #6c757d;
}
.vs__dropdown-option:hover { --select-option-role-color: var(--vs-dropdown-option--active-color); }

.select-user { max-width: 70%; }
.select-user-avatar { height: 100%; }
.select-option      { display: flex; gap: 5px; }
.select-option-name { align-content: center; overflow: hidden; text-overflow: ellipsis;}
.select-option-organization { margin-right: 5px; }
.select-option-organization-role {
    margin-left: auto;
    order: 3;
    align-content: center;
    width: 40%;
    text-align: right;
}
.select-option-role {
    font-family: "DejaVu Sans", Arial, sans-serif;
    color: var(--select-option-role-color);
}
</style>
