const globalValues = {
    /**
     * Global Datatable options
     */
    dtOptions: {
        dom: 'tilpr',
        pageLength: 10,
        serverSide: true,
        processing: true,
        language: {
            url: '/datatables/i18n/German.json',
            paginate: {
                "first":      '<i class="fa fa-angle-double-left"></id>',
                "last":       '<i class="fa fa-angle-double-right"></id>',
                "next":       '<i class="fa fa-angle-right"></id>',
                "previous":   '<i class="fa fa-angle-left"></id>',
            },
        },
        select: 'multiple',
    },
}

export {globalValues};