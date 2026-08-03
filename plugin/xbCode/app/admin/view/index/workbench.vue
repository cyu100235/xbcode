<template>
    <div class="workbench-wrapper">
        <div class="workbench" v-for="name in workbench" :key="name" :id="name">
            <component :is="name" />
        </div>
    </div>
</template>

<script>
export default {
    props: {
        workbench: {
            type: Array,
            default: () => []
        },
    },
}
</script>

<style lang="scss" scoped>
.workbench-wrapper {
    height: 100%;
    overflow: hidden;
    .workbench {
        height: 100%;
        overflow: hidden;
    }
}
</style>