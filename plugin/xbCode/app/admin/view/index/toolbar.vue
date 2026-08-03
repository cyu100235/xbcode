<template>
    <div class="toolbar-wrapper">
        <div class="toolbar" v-for="name in toolbar" :key="name" :id="name">
            <component :is="name" />
        </div>
        <div class="toolbar" id="XbAdminToolbar" v-if="toolbar.length <= 0">
            <XbAdminToolbar />
        </div>
    </div>
</template>

<script>
export default {
    props: {
        toolbar: {
            type: Array,
            default: () => []
        },
    },
}
</script>

<style lang="scss" scoped>
.toolbar-wrapper {
    height: 100%;
    overflow: hidden;
    display: flex;
    justify-content: flex-end;

    .toolbar {
        height: 100%;
        overflow: hidden;
    }
}
</style>