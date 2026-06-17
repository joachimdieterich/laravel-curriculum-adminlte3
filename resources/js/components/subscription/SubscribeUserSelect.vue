<template>
    <c-select id="subscribe-c-select"
              model="user"
              url="/users/list/subscription"
              @selectedValue="(selectedOption) => {return this.$emit('selectedValue', selectedOption);}"
              :multiple="false"
              :groupedOptions="true"
              :clearSearchOnSelect="false"
    >
        <template v-slot:option="option">
            <div class="select-option">
                <span class="select-user-avatar">
                    <img class="v-select-icon img-circle color-white select-user-avatar" alt="" :src="option.option.value.icon">
                </span>
                <div class="select-user">
                    <span class="big select-option-name">{{ option.option.label }}</span>
                    <div class="select-option-organization-role">
                        <div class="select-option-organizations">
                            <span v-for="organization in option.option.value.organizations"
                                  class="small select-option-organization"
                            >
                                {{ organization }}
                            </span>
                        </div>
                        <span class="select-option-role">{{ option.option.value.roleName }}</span>
                    </div>
                </div>
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

<style scoped>
:root {
    --select-option-role-color: #6c757d;
}
.vs__dropdown-option:hover { --select-option-role-color: var(--vs-dropdown-option--active-color); }

.select-user {
    min-width: calc(100% - 48px);;
    display: flex;
    flex-direction: row;
}
.select-user-avatar { height: 100%; }
.select-option      { display: flex; column-gap: 5px; align-items: center; }
.select-option-name { align-content: center; overflow: hidden; text-overflow: ellipsis; font-size: 1.2rem;}
.select-option-organization-role {
    margin-left: auto;
    order: 3;
    align-content: center;
    width: 40%;
    text-align: right;
}
.select-option-organizations {
    display: flex;
    column-gap: 5px;
    flex-wrap: wrap;
    justify-content: end;
}
.select-option-role {
    font-family: "DejaVu Sans", Arial, sans-serif;
    font-style: italic;
    color: var(--select-option-role-color);
}

/* Tablet and mobile */
@media (max-width: 769px) {
    .select-option {
        column-gap: 10px;
    }
    .select-user {
        max-width: 70%;
        flex-direction: column;
    }
    .select-option-organizations { justify-content: left; }
    .select-option-organization-role {
        margin-left: unset;
        text-align: left;
        width: unset;
    }
}
</style>
