<template>
    <div class='VuePagination' :class='theme.wrapper'>
        <nav :class='theme.nav'>

            <ul v-show="props.showPagination" :class="theme.list">

                <li v-if="props.hasEdgeNav" :class='theme.firstPage' @click="props.setFirstPage">
                    <a v-bind="{...props.aProps,...props.firstPageProps}">{{ props.texts.first }}</a>
                </li>

                <li v-if="props.hasChunksNav" :class='theme.prevChunk' @click="props.setPrevChunk">
                    <a v-bind="{...props.aProps, ...props.prevChunkProps}">{{ props.texts.prevChunk }}</a>
                </li>

                <li :class="theme.prev" @click="props.setPrevPage">
                    <a v-bind="{...props.aProps,...props.prevProps}">{{ props.texts.prevPage }}</a>
                </li>

                <li v-for="page in props.pages" :key="page" :class="props.pageClasses(page)"
                    v-on="props.pageEvents(page)">
                    <a v-bind="props.aProps" :class="theme.link">{{ page }}</a>
                </li>

                <li :class="theme.next" @click="props.setNextPage">
                    <a v-bind="{...props.aProps, ...props.nextProps}">{{ props.texts.nextPage }}</a>
                </li>

                <li v-if="props.hasChunksNav" :class='theme.nextChunk' @click="props.setNextChunk">
                    <a v-bind="{...props.aProps, ...props.nextChunkProps}">{{ props.texts.nextChunk }}</a>
                </li>

                <li v-if="props.hasEdgeNav" :class="theme.lastPage" @click="props.setLastPage">
                    <a v-bind="{...props.aProps, ...props.lastPageProps}">{{ props.texts.last }}</a>
                </li>

            </ul>

            <p v-show="props.hasRecords" :class='theme.count'>{{ props.count }}</p>

        </nav>
    </div>

</template>

<script>
export default {
    name: 'TailwindPagination',
    props: ['props'],

    data() {
        return {
            theme: {
                wrapper: '',
                nav: '',
                list:'list',
                firstPage:'button',
                prevChunk:'button',
                prev:'button',
                link:'pageNum',
                next:'button',
                nextChunk:'button',
                lastPage:'button',
                count:'',
            }
        }
    },
}
</script>

<style scoped type="text/sass">
.active > a{
    @apply bg-green-100;
}
.list {
    @apply flex border-collapse text-center
}
.pageNum, .button > a {
    @apply flex items-center px-3 py-1 mx-1 text-gray-500 bg-white border-gray-100 rounded-md cursor-pointer;
}

.button:hover > a[disabled] {
    @apply cursor-not-allowed;
}

.button > a:hover, .pageNum:hover {
    @apply bg-green-200;
}



</style>
