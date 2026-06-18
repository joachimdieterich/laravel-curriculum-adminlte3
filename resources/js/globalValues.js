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
    swatches: [
        ['#166534', '#16a34a', '#10b981', '#4ade80', '#6ee7b7'], // green
        ['#1e40af', '#2563eb', '#0ea5e9', '#60a5fa', '#a5b4fc'], // blue
        ['#581c87', '#a21caf', '#7c3aed', '#a855f7', '#e879f9'], // purple -> pink
        ['#991b1b', '#dc2626', '#f97316', '#f59e0b', '#facc15'], // red -> orange -> yellow
        ['#111827', '#78350f', '#9ca3af', '#d1d5db', '#f4f4f4'], // black -> brown -> grey
    ],
}

export {globalValues};