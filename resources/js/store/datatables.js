import {defineStore} from "pinia";

export const useDatatableStore = defineStore('datatable', {
    state: () => ({
        datatables: [],
        /* e.g.
            'datatable': 'user',
            'select': false || true,
            'selectedItems': []
        */
    }),
    actions: {
        addToDatatables(item) {
            let index = this.datatables?.findIndex(
                i => i.datatable === item.datatable
            );

            if (index !== -1){
                this.datatables[index] = item;
            } else {
                this.datatables.push(item);
            }
            //console.log(this.datatables);
        },
        addSelectItems(title, item){
            let datatable = this.datatables.find(dt => dt.datatable === title);

            let index = datatable.selectedItems?.findIndex(
                i => i === item
            );

            if (index === -1){
                datatable.selectedItems.push(item); //add item
            } else {
                datatable.selectedItems.splice(index, 1); //remove item
            }
            //console.log(this.datatables);
        },
        setSelectedIds(title, selection){
            let datatable = this.datatables.find(dt => dt.datatable === title);
            datatable.selectedItems = selection;
        }

    },
    getters: {
        getDatatable(state) {
            return  (title) => state.datatables.find(dt => dt.datatable === title);
        },
        isSelected (state){
            return  (title, item)  => {
                let datatable = state.datatables.find(dt => dt.datatable === title);
                let index = datatable?.selectedItems?.findIndex(
                    i => i === item
                );
                //console.log(index);
                return (index !== -1 && typeof (index) !== 'undefined') ? true : false;
            };
        },
        getSelectedItems(state) {
            return  (title) => {
                let datatable = state.datatables.find(dt => dt.datatable === title);
                return datatable?.selectedItems;
            };
        },
        getSelectedIds(state) {
            return  (title) => {
                let datatable = state.datatables.find(dt => dt.datatable === title);
                return datatable?.selectedItems.map(i => i.id);
            };
        },
        getSelectedValuesByField(state) {
            return  (title, field) => {
                let datatable = state.datatables.find(dt => dt.datatable === title);
                return datatable?.selectedItems.map(i => i[field]);
            };
        }
    },
});