<template>
    <div class="xb-config">
        <xbBuilder :schema="schema" v-if="schema || schema.length"></xbBuilder>
        <div class="empty" v-else>
            <el-empty description="获取配置项视图失败"></el-empty>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        name: String,
        schema: Object
    },
    data() {
        return {
        }
    },
    mounted() {
    },
    methods: {
    },
}
</script>

<style lang="scss" scoped>
.xb-config {
    height: 100%;

    .empty {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}
</style>