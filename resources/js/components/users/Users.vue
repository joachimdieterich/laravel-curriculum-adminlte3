<template >
    <div class="row">
        <div class="col-md-12 py-2">
            <div id="users_filter" class="dataTables_filter">
                <label >
                    <input type="search"
                           class="form-control form-control-sm"
                           placeholder="Suchbegriff"
                           v-model="search">
                </label>
            </div>
        </div>

        <div style="clear:right;"
             v-for="(item,index) in users"
             v-if="(item.firstname.toLowerCase().indexOf(search.toLowerCase()) !== -1
                || item.lastname.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                || search.length < 3"
             :id="item.id"
             v-bind:value="item.id"
             class="col-md-6">
            <div class="card mb-2">
                <div class="card-header p-1">
                    <img v-if="item.medium_id != null"
                         class="direct-chat-img "
                         :src="'/media/'+item.medium_id"
                         alt="User profile picture">
                    <avatar v-else
                            class="direct-chat-img"
                            :firstname="item.firstname"
                            :lastname="item.lastname"
                            :size="40">
                    </avatar>
                    <h3 class="card-title p-2">{{ item.firstname }}  {{ item.lastname }}</h3>

                    <div class="card-tools p-2">
                        <button v-permission="'group_enrolment'"
                                type="button"
                                class="btn btn-tool"
                                @click="expel(item.id)">
                            <i class="fa fa-user-times" ></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-12"
            v-permission="'group_enrolment'">

            <div style="clear:right;"
                class="item px-2 py-1 col-lg-6 border-right"
                v-if="!showAdd"
                @click="showSelect()">
                <i class="fa fa-plus pr-2 "></i>{{ trans('global.user.add')}}
            </div>

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
        </div>

    </div>
</template>

<script>
    import Avatar from "../uiElements/Avatar";
    export default {
        props: {
                group: {},
              },
        data() {
            return {
                users: [],
                myUsers: [],
                showAdd: false,
                search: '',
                errors: {}
            }
        },
        methods: {
            loaderEvent: function() {
                axios.get('/groups/' + this.group.id + '?json=true')
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
                            dropdownParent: $("#users").parents(),
                            allowClear: false
                        }).on('select2:select', function (e) {
                             this.enrol(e.params.data.id, this.modelId);
                        }.bind(this));
                    }).catch(e => {
                    this.errors = e.response.data.errors;
                });
            },
            async enrol(user_id) {
                try {
                    this.users = (await axios.post('/groups/enrol',
                        {
                            'enrollment_list':   {
                                0 : {
                                    'group_id' : this.group.id,
                                    'user_id' : user_id
                                }
                            }
                        })).data.users;

                } catch(error) {
                    //
                }
            },
            async expel(user_id) {
                try {
                    this.users = (await axios({
                        method: 'DELETE',
                        url: '/groups/expel',
                        data: {
                            'expel_list': {
                                0: {
                                    'group_id': this.group.id,
                                    'user_id': user_id
                                }
                            }
                        }
                    })).data.users;
                } catch(error) {
                    //
                }
            },
        },

        mounted() {
            this.loaderEvent();
        },
        components: {
            Avatar
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
