<template>
    <modal
        id="set-achievements-modal"
        name="set-achievements-modal"
        height="60%"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen()"
        @opened="opened()"
        @before-close="beforeClose()"
        style="z-index: 1100"
    >
        <div
            class="card"
            style="margin-bottom: 0px !important; height: 100%;"
        >
            <div class="card-header">
                <h3 class="card-title">
                    {{ trans('global.plan.select_users') }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                    </button>
                 </div>
            </div>
            <div class="card-body overflow-auto">
                <table id="group-datatable"></table>
            </div>
            <div class="card-footer">
                <span class="d-flex justify-content-between">
                    <button type="button" class="btn btn-default" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                    <button class="btn btn-primary" @click="submit()">{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>
<script>
export default {
    data() {
        return {

        };
    },
    mounted() {
        const dt = $('#group-datatable').DataTable({
            columns: [
                { title: 'firstname', data: 'firstname' },
                { title: 'lastname', data: 'lastname' },
            ],
            ajax: '',
        });
    },
    meethods: {
        beforeOpen() {},
        beforeClose() {},
        opened() {},
        closed() {},
    },
}
</script>