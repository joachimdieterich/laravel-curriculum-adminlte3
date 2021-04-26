<template >

    <ul class="products-list product-list-in-card">

        <li style="clear:right;"
            v-for="(item,index) in users"
            :id="item.id"
            v-bind:value="item.id"
            class="item px-2 py-1">
            {{ item.firstname }}  {{ item.lastname }}

            <span class="pull-right pr-2" ></span>

            <button class="btn btn-flat py-0 pull-right"
                >
                <i class="fa fa-user-times text-danger vuehover" ></i>
            </button>
        </li>
        <li style="clear:right;"
            class="item px-2"
            v-if="!showAdd"
            @click="showSelect()">
            <i class="fa fa-plus pr-2 "></i>{{ trans('global.user.add')}}
        </li>
        <div class="form-group pt-2"
        v-if="showAdd">
            <select name="users"
                    id="users"
                    class="form-control select2 "
                    style="width:100%;">
                <option></option>
                <option v-for="(item,index) in myUsers" v-bind:value="item.id">{{ item.firstname }} {{ item.lastname }}</option>
            </select>
        </div>
    </ul>

</template>


<script>

    export default {
        props: {
                group: {},
              },
        data() {
            return {
                users: [],
                myUsers: [],
                showAdd: false,
                errors: {}
            }
        },
        methods: {
            loaderEvent: function() {
                axios.get('/groups/' + this.group.id)
                    .then(response => {
                        this.users = JSON.parse(response.data.users);
                    }).catch(e => {
                    this.errors = e.response.data.errors;
                });
            },
            showSelect() {
                this.showAdd = true;
                axios.get('/users')
                    .then(response => {
                        this.myUsers = JSON.parse(response.data.users);
                        $("#users").select2({
                            dropdownParent: $("#users").parent(),
                            allowClear: false
                        }).on('select2:select', function (e) {
                            //this.subscribe('App\\User', e.params.data.id, this.modelId);
                        }.bind(this));
                    }).catch(e => {
                    this.errors = e.response.data.errors;
                });

            }


        },

    }
</script>
<style scoped>
.vuehover {
        display: none;
    }
li:hover .vuehover {
        display: block;
    }

</style>
