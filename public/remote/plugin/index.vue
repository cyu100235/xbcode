<template>
    <div class="plugin-container">
        <div class="title">插件管理</div>
        <div class="content">
            <div class="item" v-for="(item,index) in 15" :key="index">
                <div class="logo-container">
                    <el-image class="logo" src="https://fuss10.elemecdn.com/e/5d/4a731a90594a4af544c0c25941171jpeg.jpeg" />
                </div>
                <div class="action">
                    <el-button type="success" link>购买</el-button>
                    <!-- <el-button type="primary" link>安装</el-button> -->
                    <!-- <el-button type="warning" link>更新</el-button> -->
                    <!-- <el-button type="danger" link>卸载</el-button> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
        }
    },
    mounted() {
    },
    methods: {
        buy() { },
        install() { },
        update() { },
        uninstall() { },
    },
}
</script>

<style lang="scss">
.plugin-container {
    height: 100%;
    background-color: #fff;
    padding-bottom: 20px;

    .title {
        height:45px;
        border-bottom: 1px solid #e8e8e8;
        font-size: 16px;
        display: flex;
        align-items: center;
        padding: 0 20px;
    }

    .content {
        display: grid;
        grid-template-columns: repeat(24, 1fr);
        overflow-y: auto;
        overflow-x: hidden;
        .item {
            grid-column: span 3;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-top:15px;
            .logo-container {
                display: flex;
                justify-content: center;
                .logo {
                    width: 80px;
                    height: 80px;
                    border-radius: 5px;
                }
            }

            .action {
                display: flex;
                justify-content: center;
                align-items: center;
                padding-top: 5px;
            }
        }
    }
}</style>