<template>
    <ol class="breadcrumb ml-sm-auto">
        <li v-if="showHome"
            class="breadcrumb-item ">
            <a :href="homeURL">
                <i class="fa fa-home"></i>
            </a>
        </li>
        <li v-for="entry in entries"
            class="breadcrumb-item"
            :class="{'active': entry.active}"
            >
            <span v-if="entry.url">
                <a :href="entry.url">
                    {{ entry.title }}
                </a>
            </span>
            <span v-else>
                <strong>{{ entry.title }}</strong>
            </span>
        </li>
        <li class="breadcrumb-item ">
            <a :href="documentationURL" class="text-black-50">
                <i class="fas fa-question-circle"></i>
            </a>
        </li>
    </ol>
</template>

<script>
    export default {
        props: {
            showHome: {
                type: Boolean,
                default: true
            },
            homeURL: {
                type: String,
                default: "/"
            },
            entries: {},
            showDocumentation: {
                type: Boolean,
                default: true
            },
            documentationURL: {
                type: String,
                default: "/documentation"
            }
        }
    }
</script>
