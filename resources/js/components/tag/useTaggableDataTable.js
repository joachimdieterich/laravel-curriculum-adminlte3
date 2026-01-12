import {globalValues} from "../../globalValues.js";
import {ref} from "vue";

export default function () {
    const selectedTags = ref([]);
    const selectedNegativeTags = ref([]);

    const dtOptions = (url) => {
        let options = globalValues.dtOptions;

        options.ajax = {
            url: url,
            data: (d) => {
                d.tags = selectedTags.value;
                d.negativeTags = selectedNegativeTags.value;

                return d;
            },
        };

        return options;
    };

    return {selectedTags, selectedNegativeTags, dtOptions};
}