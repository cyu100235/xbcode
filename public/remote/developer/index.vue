<template>
    <div class="pages-container">
        <div class="title-container">
            <div class="title">开发者应用</div>
            <div class="more">
                <el-button type="info" @click="openAuth()">返回</el-button>
            </div>
        </div>
        <div class="content-container" v-if="datalist.length">
            应用列表
        </div>
        <div class="empty-container" v-else>
            <el-empty description="当前没有更多应用" />
        </div>
    </div>
</template>

<script >
export default {
    data() {
        return {
            datalist: []
        }
    },
    mounted() {
    },
    methods: {
        // 返回授权页面
        openAuth() {
            this.$emit("update:openWin", 'remote/update/auth');
        },
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    background: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;

    .title-container {
        height: 50px;
        border-bottom: 1px solid #e5e5e5;
        display: flex;
        justify-content: space-between;
        padding: 0 20px;

        .title {
            display: flex;
            align-items: center;
            font-size: 16px;
            font-weight: 700;
        }

        .more {
            display: flex;
            align-items: center;
        }
    }

    .content-container {
        flex: 1;
    }
    .empty-container {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}
</style>