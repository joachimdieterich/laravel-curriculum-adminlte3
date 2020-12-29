<template>
    <div>
        <div class="direct-chat direct-chat-primary p-0"
        style="position: relative;">
            <div class="card-header">
                <div class="d-flex justify-content-around">
                    <!--<span data-toggle="tooltip" class="badge badge-primary">{{ threads.length }}</span>-->
                    <button type="button"
                            class="btn btn-tool"
                            data-toggle="tooltip"
                            title="Threads"
                            data-widget="chat-pane-toggle"
                            @click="open('threads')">
                        <h5 class="mt-2">
                            <i class="fas fa-comments"></i>
                        </h5>
                    </button>
                    <button type="button"
                            class="btn btn-tool"
                            data-toggle="tooltip"
                            title="Contacts"
                            @click="open('contacts')">
                        <h5 class="mt-2">
                            <i class="fas fa-users"></i>
                        </h5>
                    </button>
                    <button
                        type="button"
                        class="btn btn-tool"
                        data-widget="control-sidebar"
                        data-slide="true">
                        <h5 class="mt-2">
                            <i class="fas fa-times"></i>
                        </h5>
                    </button>
                </div>

            </div>
            <!-- subheader -->
            <div  v-if="users.length > 0 && view == 'contacts'" id="chat_user_filter">
                <div class="input-group p-2">
                    <input type="search"
                           name="message"
                           v-model="search"
                           placeholder="Suchbegriff ..."
                           class="form-control">
                </div>
            </div>
            <div  v-if="view == 'newThreads'">
                <div class="bg-light p-2">
                    <h5 class="mb-0">
                        <i class="fa fa-angle-left pr-2"
                           @click="open('contacts')">
                        </i>
                        {{selectedUser.firstname}} {{selectedUser.lastname}}
                    </h5>
                </div>
            </div>
            <div  v-if="view == 'chat'">
                <div class="bg-light p-2">
                    <!--<button type="button"
                            class="btn btn-tool pull-right py-3"
                            data-toggle="tooltip"
                            title="Add user to thread"
                            @click="open('contacts')">
                        <i class="fa fa-user-plus"></i>
                    </button>-->
                    <h5 class="mb-0"
                    style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                        {{ activeThread.subject }}
                    </h5>
                </div>
            </div>
            <!-- /.subheader -->
            <!-- /.card-header -->
            <!-- Users -->
            <div class="card-body">
                <div v-if="view == 'chat'"
                     class="card-footer"
                     style="z-index:1000000 !important;">
                    <form action="#" method="post">
                        <div class="input-group">
                            <input type="text"
                                   name="message"
                                   v-model="form.message"
                                   :placeholder="trans('global.message.fields.message')+'...'"
                                   class="form-control">
                            <span class="input-group-append">
                      <button class="btn btn-primary "
                              @keyup.enter="submitMessage()"
                              @click.prevent="submitMessage()">
                            {{ trans('global.message.send') }}
                      </button>
                    </span>
                        </div>
                    </form>
                </div>
                <div class="direct-chat-messages"
                     v-if="users.length > 0 && view == 'contacts'"
                     :style="'height: '+ windowHeight +'px !important'">
                        <ul class="contacts-list">
                            <li v-for="(user, index) in users"
                                v-if="matchSearch(user)">
                                <a @click="newThread(user)"
                                   data-toggle="tooltip" title="Contacts"
                                   data-widget="chat-pane-toggle">
                                    <img v-if="user.medium_id != null"
                                         class="direct-chat-img"
                                         :src="'/media/'+user.medium_id"
                                         alt="User profile picture">
                                    <avatar v-else
                                            class="contacts-list-img"
                                            :firstname="user.firstname"
                                            :lastname="user.lastname"
                                            :size="40"
                                    ></avatar>
                                    <div class="contacts-list-info">
                                    <span class="contacts-list-name text-black link-muted">
                                          {{user.firstname}} {{user.lastname}}
                                        <small class="contacts-list-date float-right">
                                            ddd
                                        </small>
                                      </span>
                                      <span class="contacts-list-msg  link-muted">...</span>
                                </div>
                                <!-- /.contacts-list-info -->
                            </a>
                        </li>
                        <!-- End Contact Item -->
                    </ul>
                    <!-- /.contacts-list -->
                </div>
                <!-- Chat -->
                <div class="direct-chat-messages"
                     v-if="typeof activeThread === 'object' && view == 'chat'"
                    :style="'height: '+ (windowHeight - 33) +'px !important'">
                    <!-- Message. Default to the left -->
                    <div
                         v-for="(message, index) in activeThread.messages"
                         class="direct-chat-msg"
                         :class="{ 'right': message.user_id === user.id }">
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name "
                                  :class="{ 'float-left': message.user_id !== user.id, 'float-right': message.user_id === user.id }">
                                {{ message.user.firstname }} {{ message.user.lastname }}
                            </span>
                            <span class="direct-chat-timestamp"
                                  :class="{ 'float-left': message.user_id === user.id,'float-right': message.user_id !== user.id }">
                                {{ message.created_at }}
                            </span>
                        </div>
                        <!-- /.direct-chat-infos -->
                        <img v-if="message.user.medium_id != null"
                             class="direct-chat-img"
                             :src="'/media/'+message.user.medium_id"
                             alt="User profile picture">
                        <avatar v-else
                                class="direct-chat-img"
                                :firstname="message.user.firstname"
                                :lastname="message.user.lastname"
                                :size="40"
                        ></avatar>
                        <!-- /.direct-chat-img -->
                        <div class="direct-chat-text"
                             @mouseover="hover = message.id"
                             @mouseleave="hover = false">
                                <i v-if="user.id == message.user.id && hover == message.id"
                                   class="text-danger pull-right p-1 fa fa-trash pointer"
                                   @click="destroyMessage(message)"></i>
                                <i v-else-if="hover == message.id"
                                   v-can="'message_delete'"
                                   class="text-danger pull-right p-1 fa fa-trash pointer"
                                   @click="destroyMessage(message)"></i>
                            {{ message.body }}
                        </div>
                        <!-- /.direct-chat-text -->
                    </div>
                    <!-- /.direct-chat-msg -->
                </div>
                <!--/.direct-chat-messages-->

                <!-- Threads -->
                <div class="direct-chat-messages"
                     v-if="view == 'threads'"
                     :style="'height: '+ windowHeight +'px !important'">
                    <ul class="contacts-list ">
                        <li v-if="threads.length > 0"
                            v-for="(thread, index) in threads">
                            <a href="#"
                            @click="setActiveThread(thread)">
                                <img v-if="thread.messages[0].user.medium_id != null"
                                     class="direct-chat-img"
                                   :src="'/media/'+thread.messages[0].user.medium_id"
                                     alt="User profile picture">
                                <avatar v-else
                                    class="contacts-list-img"
                                    :firstname="thread.messages[0].user.firstname"
                                    :lastname="thread.messages[0].user.lastname"
                                    :size="40"
                                ></avatar>

                                <div class="contacts-list-info">
                                      <span class="contacts-list-name text-black link-muted">
                                            {{ thread.messages[0].user.firstname}} {{thread.messages[0].user.lastname}}
                                            <small class="contacts-list-date float-right">
                                                {{ diffForHumans(thread.messages[0].created_at) }}
                                            </small>
                                      </span>
                                      <span class="contacts-list-msg  link-muted">{{ thread.subject }}</span>
                                </div>
                                <!-- /.contacts-list-info -->
                            </a>
                        </li>
                        <!-- End Contact Item -->
                    </ul>
                    <!-- /.contacts-list -->
                </div>
                <!-- /.direct-chat-pane -->


                <!-- Contacts are loaded here -->
                <div class="direct-chat-messages"
                     v-if="view == 'newThreads'"
                     :style="'height: '+ windowHeight +'px !important'">
                    <form action="#" method="post">
                        <div class="form-group">
                            <input type="text"
                                   id="subject"
                                   name="subject"
                                   v-model="form.subject"
                                   :placeholder=" trans('global.message.fields.subject') "
                                   class="form-control">
                            <p class="helper-block"></p>
                            <textarea
                                   name="message"
                                   v-model="form.message"
                                   :placeholder="trans('global.message.fields.message')"
                                   class="form-control"></textarea>
                            <p class="helper-block"></p>

                              <button class="btn btn-primary pull-right"
                                      @keyup.enter="submitThread()"
                                      @click.prevent="submitThread()">
                                  {{ trans('global.message.send') }}
                              </button>

                        </div>
                    </form>
                    <!-- /.contacts-list -->
                </div>
                <!-- /.direct-chat-pane -->


            </div>
            <!-- /.card-body -->

            <!-- /.card-footer-->
        </div>
        <!--/.direct-chat -->
    </div>
</template>

<script>
    import moment from 'moment';
    import Form from 'form-backend-validation';
    import Avatar from "../uiElements/Avatar";

    export default {
        props: ['user'],
        data() {
            return {
                threads: {},
                users: {},
                activeThread: {},
                selectedUser: {},
                form: new Form({
                    'subject': '',
                    'message': '',
                    'recipients': ''
                }),
                hover: false,
                search: '',
                view:'chat',
                windowHeight: (window.innerHeight - 220),
                errors: null
            };
        },
        methods: {
            onResize() {
                this.windowHeight = (window.innerHeight - 220);
            },
            diffForHumans : function (date) {
                return moment(date).locale('de').fromNow();
            },
            setActiveThread(thread) {
                this.activeThread = thread;
                this.view = 'chat';
            },
            newThread(user) {
                this.form.recipients = [user.id];
                this.selectedUser = user;
                this.view = 'newThreads';
            },
            open(view){
                this.view = view;
            },
            submitThread() {
                this.form.submit('post', '/messages')
                    .then(response => {
                        this.activeThread = response.thread[0];
                        this.view = 'chat';
                    })
                    .catch(response => function () {
                        if (response.errors)
                        {
                            alert(response.errors);
                        }
                    });
            },
            submitMessage() {
                this.form.submit('put', '/messages/'+this.activeThread.id)
                    .then(response => {
                            this.activeThread = response.thread[0];
                    })
                    .catch(response => function () {
                        if (response.errors)
                        {
                            alert(response.errors);
                        }
                    });
            },
            matchSearch(user){
                if (user.username.toLowerCase().indexOf(this.search.toLowerCase()) === -1 &&
                    user.firstname.toLowerCase().indexOf(this.search.toLowerCase()) === -1 &&
                    user.lastname.toLowerCase().indexOf(this.search.toLowerCase()) === -1){
                    return false;
                } else {
                    return true;
                }
            },
             destroyMessage(message){

                axios.post('/messages/' + message.id + '/destroy')
                     .then(res => {
                            //this.$emit("item-added", res.data.message);
                         let index = this.activeThread.messages.indexOf(message);
                         this.activeThread.messages.splice(index, 1);

                     })
                     .catch(error => { // Handle the error returned from our request
                            this.errors = error.response.data.errors;
                     });
            },
            externalLoadMessagesEvent: function(ids) {
                axios.get('/messages')
                    .then(response => {
                        if (response.data.threads.length > 0){
                            this.threads = response.data.threads;
                            this.activeThread = this.threads[0];
                        } else {
                            this.activeThread = [];
                        }
                        this.users = response.data.users;
                    })
                    .catch(e => {
                        this.errors = error.response.data.errors;
                    });
            },
        },
        beforeMount() {

        },
        mounted(){
            this.$nextTick(() => {
                window.addEventListener('resize', this.onResize);
            })

        },
        beforeDestroy() {
            window.removeEventListener('resize', this.onResize);
        },

        components: {
            Avatar
        },

    }
</script>

