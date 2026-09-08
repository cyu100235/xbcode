<template>
    <div class="xbadmin-toolbar">
        <a
            v-for="(item, index) in toolbar"
            :key="index"
            class="xbadmin-toolbar-item"
            :href="item.url"
            :target="item.target"
            rel="noopener noreferrer"
            :title="item.tips"
        >{{ item.title }}</a>
    </div>
</template>

<script>
export default {
    props: {
        toolbar: {
            type: Array,
            default: () => [],
        },
    },
};
</script>

<style scoped>
.xbadmin-toolbar {
    display: flex;
    align-items: center;
    height: 100%;
    overflow: hidden;
}

.xbadmin-toolbar-item {
    display: inline-flex;
    align-items: center;
    height: 26px;
    padding: 0 10px;
    margin-left: 8px;
    border: 1px solid #dcdfe6;
    border-radius: 4px;
    color: #606266;
    font-size: 13px;
    line-height: 26px;
    text-decoration: none;
    white-space: nowrap;
}

.xbadmin-toolbar-item:hover {
    color: #409eff;
    border-color: #c6e2ff;
    background-color: #ecf5ff;
}
</style>
